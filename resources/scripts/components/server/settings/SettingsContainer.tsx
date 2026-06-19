import { useStoreState } from 'easy-peasy';
import isEqual from 'react-fast-compare';

import FlashMessageRender from '@/components/FlashMessageRender';
import ActionButton from '@/components/elements/ActionButton';
import Can from '@/components/elements/Can';
import CopyOnClick from '@/components/elements/CopyOnClick';
import Label from '@/components/elements/Label';
import { MainPageHeader } from '@/components/elements/MainPageHeader';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import ReinstallServerBox from '@/components/server/settings/ReinstallServerBox';

import { ip } from '@/lib/formatters';
import { t } from '@/lib/i18n';

import { ServerContext } from '@/state/server';

import RenameServerBox from './RenameServerBox';

const SettingsContainer = () => {
    const username = useStoreState((state) => state.user.data!.username);
    const id = ServerContext.useStoreState((state) => state.server.data!.id);
    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const node = ServerContext.useStoreState((state) => state.server.data!.node);
    const sftp = ServerContext.useStoreState((state) => state.server.data!.sftpDetails, isEqual);

    return (
        <ServerContentBlock title={t('strings.settings')}>
            <FlashMessageRender byKey={'settings'} />
            <MainPageHeader direction='column' title={t('strings.settings')}>
                <p className='text-sm text-neutral-400 leading-relaxed'>
                    {t('strings.settings_description')}
                </p>
            </MainPageHeader>
            <Can action={'settings.rename'}>
                <div className={`mb-6 md:mb-10`}>
                    <RenameServerBox />
                </div>
            </Can>

            <div className='w-full h-full flex flex-col gap-8'>
                <Can action={'settings.reinstall'}>
                    <ReinstallServerBox />
                </Can>
                <TitledGreyBox title={t('strings.debug_information')}>
                    <div className={`flex items-center justify-between text-sm`}>
                        <p>{t('strings.node')}</p>
                        <code className={`font-mono bg-zinc-900 rounded-sm py-1 px-2`}>{node}</code>
                    </div>
                    <CopyOnClick text={uuid}>
                        <div className={`flex items-center justify-between mt-2 text-sm`}>
                            <p>{t('strings.server_id')}</p>
                            <code className={`font-mono bg-zinc-900 rounded-sm py-1 px-2`}>{uuid}</code>
                        </div>
                    </CopyOnClick>
                </TitledGreyBox>
                <Can action={'file.sftp'}>
                    <TitledGreyBox title={t('strings.sftp_details')} className={`mb-6 md:mb-10`}>
                        <div className={`flex items-center justify-between text-sm`}>
                            <Label>{t('strings.server_address')}</Label>
                            <CopyOnClick text={`sftp://${ip(sftp.ip)}:${sftp.port}`}>
                                <code
                                    className={`font-mono bg-zinc-900 rounded-sm py-1 px-2`}
                                >{`sftp://${ip(sftp.ip)}:${sftp.port}`}</code>
                            </CopyOnClick>
                        </div>
                        <div className={`mt-2 flex items-center justify-between text-sm`}>
                            <Label>{t('strings.username')}</Label>
                            <CopyOnClick text={`${username}.${id}`}>
                                <code
                                    className={`font-mono bg-zinc-900 rounded-sm py-1 px-2`}
                                >{`${username}.${id}`}</code>
                            </CopyOnClick>
                        </div>
                        <div className={`mt-6 flex items-center`}>
                            <div className={`flex-1`}>
                                <div className={`border-l-4 border-brand p-3`}>
                                    <p className={`text-xs text-zinc-200`}>
                                        {t('strings.sftp_password_info')}
                                    </p>
                                </div>
                            </div>
                            <div className={`ml-4`}>
                                <a href={`sftp://${username}.${id}@${ip(sftp.ip)}:${sftp.port}`}>
                                    <ActionButton variant='secondary'>{t('strings.launch_sftp')}</ActionButton>
                                </a>
                            </div>
                        </div>
                    </TitledGreyBox>
                </Can>
            </div>
        </ServerContentBlock>
    );
};

export default SettingsContainer;
