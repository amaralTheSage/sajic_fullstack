import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { UserInfo } from '@/components/user-info';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import { User } from '@/types/models';
import { Link } from '@inertiajs/react';
import {
    BookMarked,
    BookPlus,
    ListChecks,
    LogOut,
    MailCheck,
    Settings,
} from 'lucide-react';
import GenerateCertificatesDialog from './generate-certificates-dialog';

export function UserMenuContent({ user }: { user: User }) {
    const cleanup = useMobileNavigation();

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <UserInfo
                        user={user}
                        showEmail={true}
                    />
                </div>
            </DropdownMenuLabel>

            <DropdownMenuSeparator />

            <DropdownMenuGroup>
                <DropdownMenuItem
                    asChild
                    className="max-md:hidden"
                >
                    <Link
                        className="block w-full"
                        href={route('user.lectures')}
                        as="button"
                        prefetch
                        onClick={cleanup}
                    >
                        <BookMarked className="mr-2" /> Minhas Palestras
                    </Link>
                </DropdownMenuItem>

                {user.is_admin ? (
                    <DropdownMenuItem
                        asChild
                        className="max-md:hidden"
                    >
                        <Link
                            className="block w-full"
                            href={route('lectures.create')}
                            as="button"
                            prefetch
                            onClick={cleanup}
                        >
                            <BookPlus className="mr-2" /> Novas Palestras
                        </Link>
                    </DropdownMenuItem>
                ) : (
                    ''
                )}

                {user.is_admin ? (
                    <DropdownMenuItem
                        asChild
                        className="max-md:hidden"
                    >
                        <Link
                            className="block w-full"
                            href={route('user.attendance_list')}
                            as="button"
                            prefetch
                            onClick={cleanup}
                        >
                            <ListChecks className="mr-2" /> Presenças
                        </Link>
                    </DropdownMenuItem>
                ) : (
                    ''
                )}

                {user.is_admin ? (
                    <GenerateCertificatesDialog>
                        <DropdownMenuItem
                            onSelect={e => e.preventDefault()}
                            className="max-md:hidden"
                        >
                            <MailCheck className="mr-2" /> Liberar Certificados
                        </DropdownMenuItem>
                    </GenerateCertificatesDialog>
                ) : (
                    ''
                )}

                <DropdownMenuSeparator className="max-md:hidden" />

                <DropdownMenuItem
                    asChild
                    className="max-md:hidden"
                >
                    <Link
                        className="block w-full"
                        href={route('password.edit')}
                        as="button"
                        prefetch
                        onClick={cleanup}
                    >
                        <Settings className="mr-2" />
                        Configurações
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>

            <DropdownMenuSeparator className="max-md:hidden" />

            <DropdownMenuItem asChild>
                <Link
                    className="block w-full"
                    method="post"
                    href={route('logout')}
                    as="button"
                    onClick={cleanup}
                >
                    <LogOut className="mr-2" />
                    Deslogar
                </Link>
            </DropdownMenuItem>
        </>
    );
}
