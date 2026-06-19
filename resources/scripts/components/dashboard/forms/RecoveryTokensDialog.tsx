import ActionButton from '@/components/elements/ActionButton';
import CopyOnClick from '@/components/elements/CopyOnClick';
import { Alert } from '@/components/elements/alert';
import { Dialog, DialogProps } from '@/components/elements/dialog';

import { t } from '@/lib/i18n';

interface RecoveryTokenDialogProps extends DialogProps {
    tokens: string[];
}

const RecoveryTokensDialog = ({ tokens, open, onClose }: RecoveryTokenDialogProps) => {
    const grouped = [] as [string, string][];
    tokens.forEach((token, index) => {
        if (index % 2 === 0) {
            grouped.push([token, tokens[index + 1] || '']);
        }
    });

    return (
        <Dialog
            open={open}
            onClose={onClose}
            title={t('strings.authenticator_app_enabled')}
            description={t('strings.authenticator_app_enabled_desc')}
            hideCloseIcon
            preventExternalClose
        >
            <Dialog.Icon position={'container'} type={'success'} />
            <CopyOnClick text={tokens.join('\n')} showInNotification={false}>
                <pre className={'bg-zinc-800 rounded-sm p-2 mt-6'}>
                    {grouped.map((value) => (
                        <span key={value.join('_')} className={'block'}>
                            {value[0]}
                            <span className={'mx-2 selection:bg-zinc-800'}>&nbsp;</span>
                            {value[1]}
                            <span className={'selection:bg-zinc-800'}>&nbsp;</span>
                        </span>
                    ))}
                </pre>
            </CopyOnClick>
            <Alert type={'danger'} className={'mt-3'}>
                {t('strings.recovery_codes_warning')}
            </Alert>
            <Dialog.Footer>
                <ActionButton variant='primary' onClick={onClose}>
                    {t('strings.done')}
                </ActionButton>
            </Dialog.Footer>
        </Dialog>
    );
};

export default RecoveryTokensDialog;
