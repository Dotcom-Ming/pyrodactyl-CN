import { TrashBin } from '@gravity-ui/icons';
import { Actions, useStoreActions } from 'easy-peasy';
import { useState } from 'react';

import ActionButton from '@/components/elements/ActionButton';
import ConfirmationModal from '@/components/elements/ConfirmationModal';

import { httpErrorToHuman } from '@/api/http';
import deleteSubuser from '@/api/server/users/deleteSubuser';
import { t } from '@/lib/i18n';

import { ApplicationStore } from '@/state';
import { ServerContext } from '@/state/server';
import { Subuser } from '@/state/server/subusers';

const RemoveSubuserButton = ({ subuser }: { subuser: Subuser }) => {
    const [loading, setLoading] = useState(false);
    const [showConfirmation, setShowConfirmation] = useState(false);

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const removeSubuser = ServerContext.useStoreActions((actions) => actions.subusers.removeSubuser);
    const { addError, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const doDeletion = () => {
        setLoading(true);
        clearFlashes('users');
        deleteSubuser(uuid, subuser.uuid)
            .then(() => {
                setLoading(false);
                removeSubuser(subuser.uuid);
                setShowConfirmation(false);
            })
            .catch((error) => {
                console.error(error);
                addError({ key: 'users', message: httpErrorToHuman(error) });
                setShowConfirmation(false);
            });
    };

    return (
        <>
            <ConfirmationModal
                title={t('server.remove_user', { username: subuser.username })}
                buttonText={t('server.remove_user_btn', { username: subuser.username })}
                visible={showConfirmation}
                loading={loading}
                onConfirmed={() => doDeletion()}
                onModalDismissed={() => setShowConfirmation(false)}
            >
                {t('server.remove_user_warning')}
            </ConfirmationModal>
            <ActionButton
                variant='danger'
                size='sm'
                className='flex items-center gap-2'
                onClick={() => setShowConfirmation(true)}
                aria-label={t('server.delete_subuser')}
            >
                <TrashBin width={22} height={22} fill='currentColor' className='w-4 h-4' />
                {t('server.delete')}
            </ActionButton>
        </>
    );
};

export default RemoveSubuserButton;
