import { faTrashAlt } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useState } from 'react';

import Code from '@/components/elements/Code';
import { Dialog } from '@/components/elements/dialog';

import { deleteSSHKey, useSSHKeys } from '@/api/account/ssh-keys';
import { t } from '@/lib/i18n';

import { useFlashKey } from '@/plugins/useFlash';

const DeleteSSHKeyButton = ({ name, fingerprint }: { name: string; fingerprint: string }) => {
    const { clearAndAddHttpError } = useFlashKey('ssh-keys');
    const [visible, setVisible] = useState(false);
    const { mutate } = useSSHKeys();

    const onClick = () => {
        clearAndAddHttpError();

        Promise.all([
            mutate((data) => data?.filter((value) => value.fingerprint !== fingerprint), false),
            deleteSSHKey(fingerprint),
        ]).catch((error) => {
            mutate(undefined, true).catch(console.error);
            clearAndAddHttpError(error);
        });
    };

    return (
        <>
            <Dialog.Confirm
                open={visible}
                title={t('server.delete_ssh_key')}
                confirm={t('server.delete_key')}
                onConfirmed={onClick}
                onClose={() => setVisible(false)}
            >
                {t('server.ssh_key_remove_named_warning_prefix')} <Code>{name}</Code>{' '}
                {t('server.ssh_key_remove_named_warning_suffix')}
            </Dialog.Confirm>
            <button
                className='p-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-all duration-150'
                onClick={() => setVisible(true)}
            >
                <FontAwesomeIcon icon={faTrashAlt} size='lg' />
            </button>
        </>
    );
};

export default DeleteSSHKeyButton;
