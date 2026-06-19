import ScreenBlock from '@/components/elements/ScreenBlock';

import { t } from '@/lib/i18n';

import { ServerContext } from '@/state/server';

import Spinner from '../elements/Spinner';

const ConflictStateRenderer = () => {
    const status = ServerContext.useStoreState((state) => state.server.data?.status || null);
    const isTransferring = ServerContext.useStoreState((state) => state.server.data?.isTransferring || false);
    const isNodeUnderMaintenance = ServerContext.useStoreState(
        (state) => state.server.data?.isNodeUnderMaintenance || false,
    );

    return status === 'installing' || status === 'install_failed' || status === 'reinstall_failed' ? (
        <div className={'flex flex-col items-center justify-center h-full'}>
            <Spinner size={'large'} />
            <div className='flex flex-col mt-4 text-center'>
                <label className='text-neutral-100 text-lg font-bold'>{t('strings.server_installing_title')}</label>
                <label className='text-neutral-500 text-md font-semibold mt-1'>
                    {t('strings.server_installing_help')}
                </label>
            </div>
        </div>
    ) : status === 'suspended' ? (
        <ScreenBlock title={t('strings.server_suspended_title')} message={t('strings.server_suspended_desc')} />
    ) : isNodeUnderMaintenance ? (
        <ScreenBlock
            title={t('strings.node_under_maintenance_title')}
            message={t('strings.node_under_maintenance_desc')}
        />
    ) : (
        <ScreenBlock
            title={isTransferring ? t('strings.transferring') : t('strings.restoring_from_backup')}
            message={
                isTransferring
                    ? t('strings.server_transferring_desc')
                    : t('strings.server_restoring_desc')
            }
        />
    );
};

export default ConflictStateRenderer;
