<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCertificate;
use App\Mail\SendCertificate;
use App\Models\Lecture;
use App\Models\LectureAttendance;
use App\Models\LectureType;
use App\Models\Room;
use App\Models\Speaker;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function PHPSTORM_META\type;

class LectureController extends Controller
{
    // Listagem de Palestras
    public function index()
    {
        $user = '';

        if (Auth::check()) {
            $user = Auth::user()->load(['lectures']);
        }

        # faz cache por 30 minuto dessa lista, puta bgl pesado rodando toda hora q o zé entra no palestras
        $lectures = Cache::remember('lectures_list', 60 * 30, function () {

            $lectures = Lecture::with(['speaker.lectures', 'speakers.lectures', 'room', 'type'])->get();

            $lectures = $lectures->sortBy(function ($lecture) {
                $date = Carbon::createFromFormat('d/m', $lecture->date);
                $startTime = Carbon::createFromFormat('H:i', $lecture->starts);
                return [$date->timestamp, $startTime->timestamp];
            })->values();

            foreach ($lectures as $l) {
                $l['n_attendees'] = DB::table('lecture_attendances')
                    ->where('lecture_id', $l->id)
                    ->count();
            }

            return $lectures;
        });



        return Inertia::render('lectures', [
            'lectures' => $lectures,
            'user' => $user,
        ]);
    }



    // GET do Form de criação de Palestras
    public function create(bool $speakerJustCreated = false)
    {
        return Inertia::render('new-lecture-form', ['speakers' => Speaker::get(), 'rooms' => Room::with('lectures')->get(), 'types' => LectureType::get()]);
    }




    //  POST de criação de Palestras
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:75',
            'speaker_ids' => 'required|array|min:1',
            'speaker_ids.*' => 'exists:speakers,id',
            'room_number' => 'required|min:3',
            'type' => 'required',
            'date' => 'required|min:5|max:5',
            'starts' => 'required|min:5|max:5',
            'ends' => 'required|min:5|max:5',
        ]);

        $lectureType = LectureType::firstOrCreate(['title' => $request['type']]);

        $lecture = Lecture::create([
            'speaker_id' => $request['speaker_ids'][0] ?? null,
            'room_number' => $request['room_number'],
            'type_id' => $lectureType->id,
            'title' => $request['title'],
            'date' => $request['date'],
            'starts' => $request['starts'],
            'ends' => $request['ends'],
        ]);

        $lecture->speakers()->sync($request['speaker_ids']);

        Cache::forget('lectures_list');

        Log::info('Admin [' . Auth::user()->email . '] adicionou palestra [' . $lecture->title . ']');

        return to_route('lectures.index');
    }

    public function edit(Lecture $lecture)
    {
        // Carrega todos os relacionamentos necessários
        $lecture->load(['type', 'speakers', 'speaker', 'room']);

        return Inertia::render('edit-lecture-form', [
            'lecture' => $lecture,
            'speakers' => Speaker::get(),
            'rooms' => Room::with('lectures')->get(),
            'types' => LectureType::get()
        ]);
    }

    // PATCH da edição de Palestra
    public function update(Request $request, Lecture $lecture)
    {
        $validated = $request->validate([
            'title' => 'required|max:75',
            'speaker_ids' => 'required|array|min:1',
            'speaker_ids.*' => 'exists:speakers,id',
            'room_number' => 'required|min:3',
            'type' => 'required',
            'date' => 'required|min:5|max:5',
            'starts' => 'required|min:5|max:5',
            'ends' => 'required|min:5|max:5',
        ]);

        $lectureType = LectureType::firstOrCreate(['title' => $request['type']]);

        $oldData = $lecture->toArray();

        $lecture->update([
            'title' => $validated['title'],
            'speaker_id' => $validated['speaker_ids'][0] ?? null,
            'room_number' => $validated['room_number'],
            'type_id' => $lectureType->id,
            'date' => $validated['date'],
            'starts' => $validated['starts'],
            'ends' => $validated['ends'],
        ]);

        // Sincroniza os palestrantes
        $lecture->speakers()->sync($validated['speaker_ids']);

        Cache::forget('lectures_list');

        Log::info('Admin [' . Auth::user()->email . '] alterou a palestra [' . $oldData['title'] . '] -> ' . json_encode($validated));

        return back();
    }

    // Deleta Palestra
    public function destroy(Lecture $lecture)
    {
        Lecture::destroy($lecture->id);

        Log::info('Admin [' . Auth::user()->email . '] deletou a palestra [' . $lecture->title . ']');

        Cache::forget('lectures_list');

        return to_route('lectures.index');
    }

    // GET Check In
    public function attendant_table(Lecture $lecture)
    {
        $lecture->load('attendants', 'speaker');

        return Inertia::render('check-in', ['lecture' => $lecture]);
    }

    // PATCH realiza o Check In
    public function checkin(Request $request, Lecture $lecture)
    {
        $validated = $request->validate([
            'checkedUsersIds' => 'required|array',
            'checkedUsersIds.*' => 'string',
        ]);

        $attendances = LectureAttendance::whereBelongsTo($lecture)
            ->whereIn('user_id', $validated['checkedUsersIds'])
            ->with('user')
            ->get();

        $newlyChecked = $attendances->where('showed_up', false);

        LectureAttendance::whereIn('id', $newlyChecked->pluck('id'))
            ->update(['showed_up' => true]);

        foreach ($newlyChecked as $attendance) {
            Mail::to($attendance->user->email)
                ->queue(new SendCertificate($lecture->title));
        }

        Log::info('Admin [' . Auth::user()->email . '] fez o checkin da palestra [' . $lecture->title . ']');

        Cache::forget('lectures_list');

        return to_route('lectures.index');
    }

    // PATCH Finaliza Palestra
    public function finish(Request $request, Lecture $lecture)
    {
        $lecture->update(['finished' => true]);

        Log::info('Admin [' . Auth::user()->email . '] fez finalizou a palestra [' . $lecture->title . ']');

        return to_route('lectures.index');
    }

    // Abre e fecha inscrições
    public function reopen_enrollment(Lecture $lecture)
    {
        $lecture->is_open_for_enrollment = ! $lecture->is_open_for_enrollment;
        $lecture->save();

        Log::info('Admin [' . Auth::user()->email . '] reabriu as inscrições para a palestra [' . $lecture->title . ']');

        Cache::forget('lectures_list');

        return back();
    }

    public function generate_certificates()
    {
        $users = LectureAttendance::select('users.id', 'users.name', 'users.email', DB::raw('COUNT(*) AS lectures'))
            ->where('showed_up', true)
            ->join('users', 'users.id', '=', 'user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->havingRaw('COUNT(*) > 2')
            ->orderBy('lectures')
            ->get();

        foreach ($users as $user) {
            ProcessCertificate::dispatch($user->toArray());
        }

        Log::info('Admin [' . Auth::user()->email . '] gerou os certificados');

        return back();
    }
}
