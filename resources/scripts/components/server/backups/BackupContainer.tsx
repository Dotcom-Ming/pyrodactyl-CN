import { ArrowDownToLine } from '@gravity-ui/icons';
import { useStoreState } from 'easy-peasy';
import { Form, Formik, Field as FormikField, FormikHelpers, useFormikContext } from 'formik';
import { createContext, lazy, useCallback, useContext, useEffect, useState } from 'react';
import { toast } from 'sonner';
import { boolean, object, string } from 'yup';

import FlashMessageRender from '@/components/FlashMessageRender';
import ActionButton from '@/components/elements/ActionButton';
import Can from '@/components/elements/Can';
import { Checkbox } from '@/components/elements/CheckboxNew';
import Field from '@/components/elements/Field';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import FormikSwitchV2 from '@/components/elements/FormikSwitchV2';
import { Textarea } from '@/components/elements/Input';
import { MainPageHeader } from '@/components/elements/MainPageHeader';
import Modal, { RequiredModalProps } from '@/components/elements/Modal';
import Pagination from '@/components/elements/Pagination';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import Spinner from '@/components/elements/Spinner';
import { PageListContainer } from '@/components/elements/pages/PageList';
import { SocketEvent } from '@/components/server/events';

import { httpErrorToHuman } from '@/api/http';
import deleteAllServerBackups from '@/api/server/backups/deleteAllServerBackups';
import { getGlobalDaemonType } from '@/api/server/getServer';
import { Context as ServerBackupContext } from '@/api/swr/getServerBackups';
import getServerBackups from '@/api/swr/getServerBackups';

import { t } from '@/lib/i18n';
import { ApplicationStore } from '@/state';
import { ServerContext } from '@/state/server';

import useFlash from '@/plugins/useFlash';
import useWebsocketEvent from '@/plugins/useWebsocketEvent';

import { useUnifiedBackups } from './useUnifiedBackups';

const BackupItemElytra = lazy(() => import('./elytra/BackupItem'));
const BackupItemWings = lazy(() => import('./wings/BackupItem'));


// Context to share live backup progress across components
export const LiveProgressContext = createContext<
    Record<
        string,
        {
            status: string;
            progress: number;
            message: string;
            canRetry: boolean;
            lastUpdated: string;
            completed: boolean;
            isDeletion: boolean;
            backupName?: string;
        }
    >
>({});

// Helper function to format storage values
const formatStorage = (mb: number | undefined | null): string => {
    if (mb === null || mb === undefined) {
        return '0MB';
    }
    if (mb >= 1024) {
        return `${(mb / 1024).toFixed(1)}GB`;
    }
    return `${mb.toFixed(1)}MB`;
};

interface BackupValues {
    name: string;
    ignored: string;
    isLocked: boolean;
}

const ModalContent = ({ ...props }: RequiredModalProps) => {
    const { isSubmitting } = useFormikContext<BackupValues>();

    return (
        <Modal {...props} showSpinnerOverlay={isSubmitting} title={t('strings.create_server_backup')}>
            <Form>
                <FlashMessageRender byKey={'backups:create'} />
                <Field
                    name={'name'}
                    label={t('strings.backup_name')}
                    description={t('strings.backup_name_description')}
                />
                <div className={`mt-6 flex flex-col`}>
                    <FormikFieldWrapper
                        className='flex flex-col gap-2'
                        name={'ignored'}
                        label={t('strings.ignored_files')}
                        description={t('strings.ignored_files_description')}
                    >
                        <FormikField
                            as={Textarea}
                            className='px-4 py-2 rounded-lg outline-hidden bg-[#ffffff17] text-sm'
                            name={'ignored'}
                            rows={6}
                        />
                    </FormikFieldWrapper>
                </div>
                <Can action={'backup.delete'}>
                    <div className={`my-6`}>
                        <FormikSwitchV2
                            name={'isLocked'}
                            label={t('strings.locked')}
                            description={t('strings.locked_description')}
                        />
                    </div>
                </Can>
                <div className={`flex justify-end mb-6`}>
                    <ActionButton variant='primary' type={'submit'} disabled={isSubmitting}>
                        {isSubmitting && <Spinner size='small' />}
                        {isSubmitting ? t('strings.creating_backup') : t('strings.start_backup')}
                    </ActionButton>
                </div>
            </Form>
        </Modal>
    );
};

const BackupContainer = () => {
    const { page, setPage } = useContext(ServerBackupContext);
    const { clearFlashes, clearAndAddHttpError, addFlash } = useFlash();
    const liveProgress = useContext(LiveProgressContext);
    const [createModalVisible, setCreateModalVisible] = useState(false);
    const [deleteAllModalVisible, setDeleteAllModalVisible] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const [deleteAllPassword, setDeleteAllPassword] = useState('');
    const [deleteAllTotpCode, setDeleteAllTotpCode] = useState('');

    // Bulk operations state
    const [selectedBackups, setSelectedBackups] = useState<Set<string>>(new Set());
    const [bulkDeleteModalVisible, setBulkDeleteModalVisible] = useState(false);
    const [isBulkDeleting, setIsBulkDeleting] = useState(false);
    const [bulkDeletePassword, setBulkDeletePassword] = useState('');
    const [bulkDeleteTotpCode, setBulkDeleteTotpCode] = useState('');
    const daemonType = getGlobalDaemonType();

    const hasTwoFactor = useStoreState((state: ApplicationStore) => state.user.data?.useTotp || false);

    const { backups, backupCount, storage, pagination, error, isValidating, createBackup, retryBackup, refresh } =
        useUnifiedBackups();

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const backupLimit = ServerContext.useStoreState((state) => state.server.data!.featureLimits.backups);
    const backupStorageLimit = ServerContext.useStoreState((state) => state.server.data!.featureLimits.backupStorageMb);

    // Check if any backup operation is in progress
    const hasActiveOperation = Object.values(liveProgress).some((op) => !op.completed);

    useEffect(() => {
        clearFlashes('backups:create');
    }, [createModalVisible]);

    const submitBackup = async (values: BackupValues, { setSubmitting }: FormikHelpers<BackupValues>) => {
        clearFlashes('backups:create');

        try {
            await createBackup(values.name, values.ignored, values.isLocked);

            // Clear any existing flash messages
            clearFlashes('backups');
            clearFlashes('backups:create');

            setSubmitting(false);
            setCreateModalVisible(false);
        } catch (error) {
            clearAndAddHttpError({ key: 'backups:create', error });
            setSubmitting(false);
        }
    };

    const handleDeleteAll = async () => {
        if (!deleteAllPassword) {
            toast.error(t('strings.password_required_delete'));
            return;
        }

        if (hasTwoFactor && !deleteAllTotpCode) {
            toast.error(t('strings.two_factor_required'));
            return;
        }

        setIsDeleting(true);

        try {
            await deleteAllServerBackups(uuid, deleteAllPassword, hasTwoFactor, deleteAllTotpCode);
            toast.success(t('strings.all_backups_deleting'));

            setDeleteAllModalVisible(false);
            setDeleteAllPassword('');
            setDeleteAllTotpCode('');

            // Websocket events will handle the UI updates automatically
        } catch (error) {
            toast.error(httpErrorToHuman(error));
        } finally {
            setIsDeleting(false);
        }
    };

    // Bulk selection handlers
    const toggleBackupSelection = (backupUuid: string) => {
        setSelectedBackups((prev) => {
            const newSet = new Set(prev);
            if (newSet.has(backupUuid)) {
                newSet.delete(backupUuid);
            } else {
                newSet.add(backupUuid);
            }
            return newSet;
        });
    };

    const toggleSelectAll = () => {
        if (selectedBackups.size === selectableBackups.length) {
            setSelectedBackups(new Set());
        } else {
            setSelectedBackups(new Set(selectableBackups.map((b) => b.uuid)));
        }
    };

    const clearSelection = () => {
        setSelectedBackups(new Set());
    };

    // Get backups that can be selected (completed and not active)
    const selectableBackups = backups.filter((b) => b.status === 'completed' && b.isSuccessful && !b.isLiveOnly);

    const handleBulkDelete = async () => {
        if (!bulkDeletePassword) {
            addFlash({
                key: 'backups:bulk_delete',
                type: 'error',
                message: t('strings.password_required_delete'),
            });
            return;
        }

        if (hasTwoFactor && !bulkDeleteTotpCode) {
            addFlash({
                key: 'backups:bulk_delete',
                type: 'error',
                message: t('strings.two_factor_required'),
            });
            return;
        }

        setIsBulkDeleting(true);
        clearFlashes('backups:bulk_delete');

        try {
            const http = (await import('@/api/http')).default;
            await http.post(`/api/client/servers/${uuid}/backups/bulk-delete`, {
                backup_uuids: Array.from(selectedBackups),
                password: bulkDeletePassword,
                ...(hasTwoFactor ? { totp_code: bulkDeleteTotpCode } : {}),
            });

            addFlash({
                key: 'backups',
                type: 'success',
                message: `${selectedBackups.size} backup${selectedBackups.size > 1 ? 's are' : ' is'} being deleted.`,
            });

            setBulkDeleteModalVisible(false);
            setBulkDeletePassword('');
            setBulkDeleteTotpCode('');
            clearSelection();

            // Refresh the backup list to reflect the deletions
            await refresh();
        } catch (error) {
            clearAndAddHttpError({ key: 'backups:bulk_delete', error });
        } finally {
            setIsBulkDeleting(false);
        }
    };

    useEffect(() => {
        if (!error) {
            clearFlashes('backups');
            return;
        }
        clearAndAddHttpError({ error, key: 'backups' });
    }, [error]);

    if (!backups || (error && isValidating)) {
        return (
            <ServerContentBlock title={t('strings.backups')}>
                <FlashMessageRender byKey={'backups'} />
                <MainPageHeader direction='column' title={t('strings.backups')}>
                    <p className='text-sm text-neutral-400 leading-relaxed'>
                        {t('strings.backups_description')}
                    </p>
                </MainPageHeader>
                <div className='flex items-center justify-center py-12'>
                    <div className='animate-spin rounded-full h-8 w-8 border-b-2 border-brand'></div>
                </div>
            </ServerContentBlock>
        );
    }

    return (
        <ServerContentBlock title={t('strings.backups')}>
            <FlashMessageRender byKey={'backups'} />
            <MainPageHeader
                direction='column'
                title={t('strings.backups')}
                titleChildren={
                    <Can action={'backup.create'}>
                        <div className='flex flex-col sm:flex-row items-center justify-end gap-4'>
                            <div className='flex flex-col gap-1 text-center sm:text-right'>
                                {/* Backup Count Display */}
                                {backupLimit === null && <p className='text-sm text-zinc-300'>{backupCount} backups</p>}
                                {backupLimit > 0 && (
                                    <p className='text-sm text-zinc-300'>
                                        {backupCount} of {backupLimit} backups
                                    </p>
                                )}
                                {backupLimit === 0 && <p className='text-sm text-red-400'>{t('strings.backups_disabled')}</p>}

                                {/* Storage Usage Display */}
                                {storage && (
                                    <div className='flex flex-col gap-0.5'>
                                        {backupStorageLimit === null ? (
                                            <>
                                                <p
                                                    className='text-sm text-zinc-300 cursor-help'
                                                    title={`${storage.used_mb?.toFixed(2) || 0}MB total (Repository: ${storage.repository_usage_mb?.toFixed(2) || 0}MB, Legacy: ${storage.legacy_usage_mb?.toFixed(2) || 0}MB)`}
                                                >
                                                    <span className='font-medium'>
                                                        {formatStorage(storage.used_mb)}
                                                    </span>{' '}
                                                    {t('strings.storage_used')}
                                                </p>
                                                {(storage.repository_usage_mb > 0 || storage.legacy_usage_mb > 0) &&
                                                    storage.repository_usage_mb > 0 &&
                                                    storage.legacy_usage_mb > 0 && (
                                                        <p className='text-xs text-zinc-400'>
                                                            {storage.repository_usage_mb > 0 &&
                                                                `${formatStorage(storage.repository_usage_mb)} ${t('strings.deduplicated')}`}
                                                            {storage.repository_usage_mb > 0 &&
                                                                storage.legacy_usage_mb > 0 &&
                                                                ' + '}
                                                            {storage.legacy_usage_mb > 0 &&
                                                                `${formatStorage(storage.legacy_usage_mb)} ${t('strings.legacy')}`}
                                                        </p>
                                                    )}
                                            </>
                                        ) : (
                                            <>
                                                <p
                                                    className='text-sm text-zinc-300 cursor-help'
                                                    title={`${storage.used_mb?.toFixed(2) || 0}MB used of ${backupStorageLimit}MB (Repository: ${storage.repository_usage_mb?.toFixed(2) || 0}MB, Legacy: ${storage.legacy_usage_mb?.toFixed(2) || 0}MB, ${storage.available_mb?.toFixed(2) || 0}MB Available)`}
                                                >
                                                    <span className='font-medium'>
                                                        {formatStorage(storage.used_mb)}
                                                    </span>{' '}
                                                    {backupStorageLimit === null ? (
                                                        'used'
                                                    ) : (
                                                        <span className='font-medium'>
                                                            {t('strings.of_limit_used', { limit: formatStorage(backupStorageLimit) })}
                                                        </span>
                                                    )}
                                                </p>
                                                {(storage.repository_usage_mb > 0 || storage.legacy_usage_mb > 0) &&
                                                    storage.repository_usage_mb > 0 &&
                                                    storage.legacy_usage_mb > 0 && (
                                                        <p className='text-xs text-zinc-400'>
                                                            {storage.repository_usage_mb > 0 &&
                                                                `${formatStorage(storage.repository_usage_mb)} ${t('strings.deduplicated')}`}
                                                            {storage.repository_usage_mb > 0 &&
                                                                storage.legacy_usage_mb > 0 &&
                                                                ' + '}
                                                            {storage.legacy_usage_mb > 0 &&
                                                                `${formatStorage(storage.legacy_usage_mb)} ${t('strings.legacy')}`}
                                                        </p>
                                                    )}
                                            </>
                                        )}
                                    </div>
                                )}
                            </div>
                            <div className='flex gap-2'>
                                {backupCount > 0 && (
                                    <ActionButton
                                        variant='danger'
                                        onClick={() => setDeleteAllModalVisible(true)}
                                        disabled={hasActiveOperation}
                                    >
                                        <svg
                                            className='w-4 h-4 mr-2'
                                            fill='none'
                                            viewBox='0 0 24 24'
                                            stroke='currentColor'
                                        >
                                            <path
                                                strokeLinecap='round'
                                                strokeLinejoin='round'
                                                strokeWidth={2}
                                                d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
                                            />
                                        </svg>
                                        {t('strings.delete_all_backups')}
                                    </ActionButton>
                                )}
                                {(backupLimit === null || backupLimit > backupCount) &&
                                    (!backupStorageLimit || !storage?.is_over_limit) && (
                                        <ActionButton
                                            variant='primary'
                                            onClick={() => setCreateModalVisible(true)}
                                            disabled={hasActiveOperation}
                                        >
                                            {t('strings.new_backup')}
                                        </ActionButton>
                                    )}
                            </div>
                        </div>
                    </Can>
                }
            >
                <p className='text-sm text-neutral-400 leading-relaxed'>
                    {t('strings.backups_description')}
                </p>
            </MainPageHeader>

            {createModalVisible && (
                <Formik
                    onSubmit={submitBackup}
                    initialValues={{ name: '', ignored: '', isLocked: false }}
                    validationSchema={object().shape({
                        name: string().max(191),
                        ignored: string(),
                        isLocked: boolean(),
                    })}
                >
                    <ModalContent visible={createModalVisible} onDismissed={() => setCreateModalVisible(false)} />
                </Formik>
            )}

            {deleteAllModalVisible && (
                <Modal
                    visible={deleteAllModalVisible}
                    onDismissed={() => {
                        setDeleteAllModalVisible(false);
                        setDeleteAllPassword('');
                        setDeleteAllTotpCode('');
                    }}
                    title={t('strings.delete_all_backups_title')}
                >
                    <div className='space-y-4'>
                        <p className='text-sm text-zinc-300'>
                            {t('strings.delete_all_warning')}
                        </p>

                        <div className='p-4 bg-red-500/10 border border-red-500/20 rounded-lg'>
                            <div className='flex items-start gap-3'>
                                <svg
                                    className='w-5 h-5 text-red-400 mt-0.5 flex-shrink-0'
                                    fill='none'
                                    viewBox='0 0 24 24'
                                    stroke='currentColor'
                                >
                                    <path
                                        strokeLinecap='round'
                                        strokeLinejoin='round'
                                        strokeWidth={2}
                                        d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
                                    />
                                </svg>
                                <div className='text-sm'>
                                    <p className='font-medium text-red-300'>{t('strings.this_action_cannot_be_undone')}</p>
                                    <ul className='text-red-400 mt-2 space-y-1 list-disc list-inside'>
                                        <li>{t('strings.all_backup_data_deleted')}</li>
                                        <li>{t('strings.locked_backups_deleted')}</li>
                                        <li>{t('strings.backup_repository_destroyed')}</li>
                                        <li>{t('strings.may_take_several_minutes')}</li>
                                        <li>{t('strings.cannot_restore_backups')}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div className='space-y-3'>
                            <div>
                                <label htmlFor='password' className='block text-sm font-medium text-zinc-300 mb-1'>
                                    {t('strings.password')}
                                </label>
                                <input
                                    id='password'
                                    type='password'
                                    className='w-full px-4 py-2 rounded-lg outline-hidden bg-[#ffffff17] text-sm border border-zinc-700 focus:border-brand'
                                    placeholder={t('strings.enter_password')}
                                    value={deleteAllPassword}
                                    onChange={(e) => setDeleteAllPassword(e.target.value)}
                                    disabled={isDeleting}
                                />
                            </div>

                            {hasTwoFactor && (
                                <div>
                                    <label htmlFor='totp_code' className='block text-sm font-medium text-zinc-300 mb-1'>
                                        {t('strings.two_factor_code')}
                                    </label>
                                    <input
                                        id='totp_code'
                                        type='text'
                                        className='w-full px-4 py-2 rounded-lg outline-hidden bg-[#ffffff17] text-sm border border-zinc-700 focus:border-brand'
                                        placeholder={t('strings.six_digit_code')}
                                        maxLength={6}
                                        value={deleteAllTotpCode}
                                        onChange={(e) => setDeleteAllTotpCode(e.target.value.replace(/[^0-9]/g, ''))}
                                        disabled={isDeleting}
                                    />
                                </div>
                            )}
                        </div>

                        <div className='flex justify-end gap-3 pb-6 pt-2'>
                            <ActionButton
                                variant='secondary'
                                onClick={() => {
                                    setDeleteAllModalVisible(false);
                                    setDeleteAllPassword('');
                                    setDeleteAllTotpCode('');
                                }}
                                disabled={isDeleting}
                            >
                                {t('strings.cancel')}
                            </ActionButton>
                            <ActionButton variant='danger' onClick={handleDeleteAll} disabled={isDeleting}>
                                {isDeleting && <Spinner size='small' />}
                                {isDeleting ? t('strings.deleting_backups') : t('strings.delete_all_backups')}
                            </ActionButton>
                        </div>
                    </div>
                </Modal>
            )}

            {/* Bulk delete modal */}
            {bulkDeleteModalVisible && (
                <Modal
                    visible={bulkDeleteModalVisible}
                    onDismissed={() => {
                        setBulkDeleteModalVisible(false);
                        setBulkDeletePassword('');
                        setBulkDeleteTotpCode('');
                    }}
                    title={t('strings.delete_selected_backups')}
                >
                    <FlashMessageRender byKey={'backups:bulk_delete'} />
                    <div className='space-y-4'>
                        <p className='text-sm text-zinc-300'>
                            {t('strings.selected_backups_deleted')}
                        </p>

                        <div className='p-4 bg-red-500/10 border border-red-500/20 rounded-lg'>
                            <div className='flex items-start gap-3'>
                                <svg
                                    className='w-5 h-5 text-red-400 mt-0.5 flex-shrink-0'
                                    fill='none'
                                    viewBox='0 0 24 24'
                                    stroke='currentColor'
                                >
                                    <path
                                        strokeLinecap='round'
                                        strokeLinejoin='round'
                                        strokeWidth={2}
                                        d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
                                    />
                                </svg>
                                <div className='text-sm'>
                                    <p className='font-medium text-red-300'>{t('strings.warning')}</p>
                                    <p className='text-red-400 mt-1'>
                                        {t('strings.selected_backups_deleted')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div className='space-y-3'>
                            <div>
                                <label htmlFor='bulk-password' className='block text-sm font-medium text-zinc-300 mb-1'>
                                    {t('strings.password')}
                                </label>
                                <input
                                    id='bulk-password'
                                    type='password'
                                    className='w-full px-4 py-2 rounded-lg outline-hidden bg-[#ffffff17] text-sm border border-zinc-700 focus:border-brand'
                                    placeholder={t('strings.enter_password')}
                                    value={bulkDeletePassword}
                                    onChange={(e) => setBulkDeletePassword(e.target.value)}
                                    disabled={isBulkDeleting}
                                />
                            </div>

                            {hasTwoFactor && (
                                <div>
                                    <label htmlFor='bulk-totp' className='block text-sm font-medium text-zinc-300 mb-1'>
                                        {t('strings.two_factor_code')}
                                    </label>
                                    <input
                                        id='bulk-totp'
                                        type='text'
                                        className='w-full px-4 py-2 rounded-lg outline-hidden bg-[#ffffff17] text-sm border border-zinc-700 focus:border-brand'
                                        placeholder={t('strings.six_digit_code')}
                                        maxLength={6}
                                        value={bulkDeleteTotpCode}
                                        onChange={(e) => setBulkDeleteTotpCode(e.target.value.replace(/[^0-9]/g, ''))}
                                        disabled={isBulkDeleting}
                                    />
                                </div>
                            )}
                        </div>

                        <div className='flex justify-end gap-3 pb-6 pt-2'>
                            <ActionButton
                                variant='secondary'
                                onClick={() => {
                                    setBulkDeleteModalVisible(false);
                                    setBulkDeletePassword('');
                                    setBulkDeleteTotpCode('');
                                }}
                                disabled={isBulkDeleting}
                            >
                                {t('strings.cancel')}
                            </ActionButton>
                            <ActionButton variant='danger' onClick={handleBulkDelete} disabled={isBulkDeleting}>
                                {isBulkDeleting && <Spinner size='small' />}
                                {isBulkDeleting
                                    ? t('strings.deleting_backups')
                                    : t('strings.delete_selected_count', { count: selectedBackups.size })}
                            </ActionButton>
                        </div>
                    </div>
                </Modal>
            )}

            {backups.length === 0 ? (
                <div className='flex flex-col items-center justify-center min-h-[60vh] py-12 px-4'>
                    <div className='text-center'>
                        <div className='w-16 h-16 mx-auto mb-4 rounded-full bg-[#ffffff11] flex items-center justify-center'>
                            <ArrowDownToLine
                                width={22}
                                height={22}
                                className='w-6 h-6 text-zinc-400'
                                fill=' currentColor'
                            />
                        </div>
                        <h3 className='text-lg font-medium text-zinc-200 mb-2'>
                            {backupLimit === 0 ? t('strings.backups_unavailable') : t('strings.no_backups')}
                        </h3>
                        <p className='text-sm text-zinc-400 max-w-sm'>
                            {backupLimit === 0
                                ? t('strings.backups_cannot_be_created')
                                : t('strings.no_backups_help')}
                        </p>
                    </div>
                </div>
            ) : (
                <>
                    {/* Bulk action bar */}
                    {selectableBackups.length > 0 && (
                        <div className='mb-8 flex items-center justify-between px-4 py-3.5 rounded-xl bg-[#ffffff08] border border-zinc-700'>
                            <div className='flex items-center gap-4'>
                                <Checkbox
                                    checked={
                                        selectedBackups.size === selectableBackups.length &&
                                        selectableBackups.length > 0
                                    }
                                    onCheckedChange={toggleSelectAll}
                                />
                                <span className='text-sm text-zinc-300'>
                                    {selectedBackups.size > 0 ? (
                                        <>
                                            <span className='font-medium'>{selectedBackups.size}</span> {t('strings.selected')}
                                        </>
                                    ) : (
                                        t('strings.select_backups')
                                    )}
                                </span>
                            </div>

                            <div
                                className={`flex items-center gap-3 transition-opacity ${selectedBackups.size > 0 ? 'opacity-100' : 'opacity-0 pointer-events-none'}`}
                            >
                                <ActionButton variant='secondary' onClick={clearSelection}>
                                    {t('strings.clear')}
                                </ActionButton>
                                <Can action='backup.delete'>
                                    <ActionButton variant='danger' onClick={() => setBulkDeleteModalVisible(true)}>
                                        {t('strings.delete_selected_count', { count: selectedBackups.size })}
                                    </ActionButton>
                                </Can>
                            </div>
                        </div>
                    )}

                    <PageListContainer>
                        {backups.map((backup) =>
                            daemonType === 'elytra' ? (
                                <BackupItemElytra
                                    key={backup.uuid}
                                    backup={backup}
                                    isSelected={selectedBackups.has(backup.uuid)}
                                    onToggleSelect={() => toggleBackupSelection(backup.uuid)}
                                    isSelectable={selectableBackups.some((b) => b.uuid === backup.uuid)}
                                    retryBackup={retryBackup}
                                />
                            ) : (
                                <BackupItemWings key={backup.uuid} backup={backup} />
                            ),
                        )}
                    </PageListContainer>

                    {pagination && pagination.currentPage && pagination.totalPages && pagination.totalPages > 1 && (
                        <Pagination data={{ items: backups, pagination }} onPageSelect={setPage}>
                            {() => null}
                        </Pagination>
                    )}
                </>
            )}
        </ServerContentBlock>
    );
};

const BackupContainerWrapper = () => {
    const [page, setPage] = useState<number>(1);
    const { mutate } = getServerBackups();
    const [liveProgress, setLiveProgress] = useState<
        Record<
            string,
            {
                status: string;
                progress: number;
                message: string;
                canRetry: boolean;
                lastUpdated: string;
                completed: boolean;
                isDeletion: boolean;
                backupName?: string;
            }
        >
    >({});

    // Single websocket listener for the entire page
    const handleBackupStatus = useCallback(
        (rawData: any) => {
            let data;
            try {
                if (typeof rawData === 'string') {
                    data = JSON.parse(rawData);
                } else {
                    data = rawData;
                }
            } catch (error) {
                return;
            }

            const backup_uuid = data?.backup_uuid;
            if (!backup_uuid) {
                return;
            }

            const { status, progress, message, timestamp, operation, error: errorMsg, name } = data;

            const can_retry = status === 'failed' && operation === 'create';
            const last_updated_at = timestamp ? new Date(timestamp * 1000).toISOString() : new Date().toISOString();
            const isDeletionOperation = operation === 'delete' || data.deleted === true;

            setLiveProgress((prevProgress) => {
                const currentState = prevProgress[backup_uuid];
                const newProgress = progress || 0;
                const isCompleted = status === 'completed' && newProgress === 100;
                const displayMessage = errorMsg ? `${message || 'Operation failed'}: ${errorMsg}` : message || '';

                if (currentState?.completed && !isCompleted) {
                    return prevProgress;
                }

                if (
                    currentState &&
                    !isCompleted &&
                    currentState.lastUpdated >= last_updated_at &&
                    currentState.progress >= newProgress
                ) {
                    return prevProgress;
                }

                return {
                    ...prevProgress,
                    [backup_uuid]: {
                        status,
                        progress: newProgress,
                        message: displayMessage,
                        canRetry: can_retry || false,
                        lastUpdated: last_updated_at,
                        completed: isCompleted,
                        isDeletion: isDeletionOperation,
                        backupName: name || currentState?.backupName,
                    },
                };
            });

            if (status === 'completed' && progress === 100) {
                if (isDeletionOperation) {
                    // Optimistically remove the deleted backup from SWR cache immediately
                    // note: this is incredibly buggy sometimes, somebody please refactor how "live" backups work. - ellie
                    // Changed this to use "revalidate: false" so the optimistic update persists - tyr
                    mutate(
                        (currentData) => {
                            if (!currentData) return currentData;
                            return {
                                ...currentData,
                                items: currentData.items.filter((b) => b.uuid !== backup_uuid),
                                backupCount: Math.max(0, (currentData.backupCount || 0) - 1),
                            };
                        },
                        { revalidate: false },
                    );

                    // Remove from live progress immediately
                    setLiveProgress((prev) => {
                        const updated = { ...prev };
                        delete updated[backup_uuid];
                        return updated;
                    });
                } else {
                    // For new backups, wait for them to appear in the API
                    mutate();
                    const checkForBackup = async (attempts = 0) => {
                        if (attempts > 10) {
                            setLiveProgress((prev) => {
                                const updated = { ...prev };
                                delete updated[backup_uuid];
                                return updated;
                            });
                            return;
                        }

                        // Force fresh data
                        const currentBackups = await mutate();
                        const backupExists = currentBackups?.items?.some((b) => b.uuid === backup_uuid);

                        if (backupExists) {
                            setLiveProgress((prev) => {
                                const updated = { ...prev };
                                delete updated[backup_uuid];
                                return updated;
                            });
                        } else {
                            setTimeout(() => checkForBackup(attempts + 1), 1000);
                        }
                    };

                    setTimeout(() => checkForBackup(), 1000);
                }
            }
        },
        [mutate],
    );

    useWebsocketEvent(SocketEvent.BACKUP_STATUS, handleBackupStatus);

    return (
        <LiveProgressContext.Provider value={liveProgress}>
            <ServerBackupContext.Provider value={{ page, setPage }}>
                <BackupContainer />
            </ServerBackupContext.Provider>
        </LiveProgressContext.Provider>
    );
};

export default BackupContainerWrapper;
