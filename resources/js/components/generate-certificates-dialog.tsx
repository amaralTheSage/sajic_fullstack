import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { router } from '@inertiajs/react';
import { ReactNode, useState } from 'react';
import { toast } from 'sonner';
import { Button } from './ui/button';

function GenerateCertificatesDialog({ children }: { children: ReactNode }) {
    const [open, setOpen] = useState(false);

    function handleConfirm() {
        router.post(
            route('lectures.certificates'),
            {},
            {
                onStart: () => {
                    toast.info(
                        'A geração de certificados foi iniciada. Isso pode demorar um pouco.',
                    );
                },
                onSuccess: () => {
                    toast.success(
                        'Os certificados estão sendo gerados e enviados em segundo plano.',
                    );
                },
                onError: e => {
                    console.error(e);
                    toast.error(
                        'Ocorreu um erro ao tentar liberar os certificados.',
                    );
                },
                onFinish: () => {
                    setOpen(false);
                },
            },
        );
    }

    return (
        <Dialog
            open={open}
            onOpenChange={setOpen}
        >
            <DialogTrigger asChild>{children}</DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Liberar Certificados?</DialogTitle>
                    <DialogDescription>
                        Esta ação irá gerar e enviar por e-mail os certificados de
                        todas as palestras finalizadas para os participantes que
                        tiveram presença. Isso pode demorar um pouco e a ação
                        não pode ser desfeita.
                    </DialogDescription>
                </DialogHeader>
                <Button
                    className="ml-auto w-fit"
                    onClick={handleConfirm}
                >
                    Sim, liberar certificados
                </Button>
            </DialogContent>
        </Dialog>
    );
}

export default GenerateCertificatesDialog;
