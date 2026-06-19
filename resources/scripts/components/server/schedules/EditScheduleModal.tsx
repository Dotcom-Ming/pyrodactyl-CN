import ModalContext from '@/context/ModalContext';
import { TZDate } from '@date-fns/tz';
import { Link, TriangleExclamation } from '@gravity-ui/icons';
import { toString } from 'cronstrue';
import { format } from 'date-fns';
import { useStoreState } from 'easy-peasy';
import { Form, Formik, FormikHelpers } from 'formik';
import { useContext, useEffect, useMemo } from 'react';

import FlashMessageRender from '@/components/FlashMessageRender';
import ActionButton from '@/components/elements/ActionButton';
import Field from '@/components/elements/Field';
import FormikSwitchV2 from '@/components/elements/FormikSwitchV2';
import ItemContainer from '@/components/elements/ItemContainer';

import asModal from '@/hoc/asModal';

import { httpErrorToHuman } from '@/api/http';
import createOrUpdateSchedule from '@/api/server/schedules/createOrUpdateSchedule';
import { Schedule } from '@/api/server/schedules/getServerSchedules';

import { ServerContext } from '@/state/server';

import useFlash from '@/plugins/useFlash';
import { t } from '@/lib/i18n';

interface Props {
    schedule?: Schedule;
}

interface Values {
    name: string;
    dayOfWeek: string;
    month: string;
    dayOfMonth: string;
    hour: string;
    minute: string;
    enabled: boolean;
    onlyWhenOnline: boolean;
}

const getTimezoneInfo = (serverTimezone: string) => {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    const now = new Date();

    const userOffsetString = format(now, 'xxx');
    let serverOffsetString: string;
    let offsetDifferenceMinutes = 0;

    let isServerTimezoneValid = true;
    try {
        const serverDate = new TZDate(now, serverTimezone);
        const userDate = new TZDate(now, userTimezone);
        serverOffsetString = format(serverDate, 'xxx');

        // offset difference in minutes
        const serverOffsetValue = serverDate.getTimezoneOffset();
        const userOffsetValue = userDate.getTimezoneOffset();

        // + values mean behind UTC
        // - values mean ahead of UTC
        offsetDifferenceMinutes = userOffsetValue - serverOffsetValue;
    } catch {
        serverOffsetString = 'Unknown';
        isServerTimezoneValid = false;
    }

    let differenceDescription = '';
    if (!isServerTimezoneValid) {
        differenceDescription = 'at an unknown difference to';
    } else if (offsetDifferenceMinutes === 0) {
        differenceDescription = 'same time';
    } else {
        const offsetDifferenceHours = offsetDifferenceMinutes / 60;
        const absDifferenceHours = Math.abs(offsetDifferenceHours);
        const isAhead = offsetDifferenceMinutes > 0;

        if (absDifferenceHours === Math.floor(absDifferenceHours)) {
            // whole hours
            differenceDescription = `${absDifferenceHours} hour${absDifferenceHours !== 1 ? 's' : ''} ${isAhead ? 'ahead of' : 'behind'}`;
        } else {
            // hours & minutes
            const hours = Math.floor(absDifferenceHours);
            const minutes = Math.abs(offsetDifferenceMinutes % 60);

            if (hours > 0) {
                differenceDescription = `${hours}h ${minutes}m ${isAhead ? 'ahead of' : 'behind'}`;
            } else {
                differenceDescription = `${minutes} minute${minutes !== 1 ? 's' : ''} ${isAhead ? 'ahead of' : 'behind'}`;
            }
        }
    }

    return {
        user: { timezone: userTimezone, offset: userOffsetString },
        server: { timezone: serverTimezone, offset: serverOffsetString },
        difference: differenceDescription,
        isDifferent: userTimezone !== serverTimezone,
    };
};

const formatTimezoneDisplay = (timezone: string, offset: string) => {
    return `${timezone} (${offset})`;
};

const getCronDescription = (
    minute: string,
    hour: string,
    dayOfMonth: string,
    month: string,
    dayOfWeek: string,
): string => {
    try {
        // Build cron expression: minute hour dayOfMonth month dayOfWeek
        const cronExpression = `${minute} ${hour} ${dayOfMonth} ${month} ${dayOfWeek}`;
        const description = toString(cronExpression, {
            throwExceptionOnParseError: false,
            verbose: true,
        });

        // Check if cronstrue returned an error message
        if (
            description ===
            'An error occurred when generating the expression description. Check the cron expression syntax.'
        ) {
            return 'Invalid cron expression';
        }

        return description;
    } catch {
        return 'Invalid cron expression.';
    }
};

const EditScheduleModal = ({ schedule }: Props) => {
    const { addError, clearFlashes } = useFlash();
    const { dismiss, setPropOverrides } = useContext(ModalContext);

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const appendSchedule = ServerContext.useStoreActions((actions) => actions.schedules.appendSchedule);
    const serverTimezone = useStoreState((state) => state.settings.data?.timezone || 'Unknown');

    const timezoneInfo = useMemo(() => {
        return getTimezoneInfo(serverTimezone);
    }, [serverTimezone]);

    useEffect(() => {
        setPropOverrides({ title: schedule ? t('server.edit_schedule') : t('server.create_new_schedule') });
    }, []);

    useEffect(() => {
        return () => {
            clearFlashes('schedule:edit');
        };
    }, []);

    const submit = (values: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('schedule:edit');
        createOrUpdateSchedule(uuid, {
            id: schedule?.id,
            name: values.name,
            cron: {
                minute: values.minute,
                hour: values.hour,
                dayOfWeek: values.dayOfWeek,
                month: values.month,
                dayOfMonth: values.dayOfMonth,
            },
            onlyWhenOnline: values.onlyWhenOnline,
            isActive: values.enabled,
        })
            .then((schedule) => {
                setSubmitting(false);
                appendSchedule(schedule);
                dismiss();
            })
            .catch((error) => {
                console.error(error);

                setSubmitting(false);
                addError({ key: 'schedule:edit', message: httpErrorToHuman(error) });
            });
    };

    return (
        <Formik
            onSubmit={submit}
            initialValues={
                {
                    name: schedule?.name || '',
                    minute: schedule?.cron.minute || '*/5',
                    hour: schedule?.cron.hour || '*',
                    dayOfMonth: schedule?.cron.dayOfMonth || '*',
                    month: schedule?.cron.month || '*',
                    dayOfWeek: schedule?.cron.dayOfWeek || '*',
                    enabled: schedule?.isActive ?? true,
                    onlyWhenOnline: schedule?.onlyWhenOnline ?? true,
                } as Values
            }
        >
            {({ isSubmitting, values }) => {
                const cronDescription = getCronDescription(
                    values.minute,
                    values.hour,
                    values.dayOfMonth,
                    values.month,
                    values.dayOfWeek,
                );

                return (
                    <Form>
                        <FlashMessageRender byKey={'schedule:edit'} />
                        <Field
                            name={'name'}
                            label={t('server.schedule_name')}
                            description={t('server.schedule_name_desc')}
                        />
                        <div className={`grid grid-cols-2 sm:grid-cols-5 gap-4 mt-6`}>
                            <Field name={'minute'} label={t('server.minute')} />
                            <Field name={'hour'} label={t('server.hour')} />
                            <Field name={'dayOfWeek'} label={t('server.day_week')} />
                            <Field name={'dayOfMonth'} label={t('server.day_month')} />
                            <Field name={'month'} label={t('server.month')} />
                        </div>

                        <div className={`mt-3 p-3 rounded-lg bg-zinc-800/50 border border-zinc-700/50`}>
                            <p className={`text-sm text-zinc-200 font-medium`}>{cronDescription}</p>
                        </div>

                        <p className={`text-zinc-400 text-xs mt-2`}>
                            {t('server.cron_desc')}
                        </p>

                        {timezoneInfo.isDifferent && (
                            <div className={'bg-blue-900/20 border border-blue-400/30 rounded-lg p-4 my-2'}>
                                <div className={'flex items-start gap-3'}>
                                    <TriangleExclamation
                                        width={22}
                                        height={22}
                                        fill='currentColor'
                                        className={'text-blue-400 mt-0.5 flex-shrink-0 h-5 w-5'}
                                    />
                                    <div className={'text-sm'}>
                                        <p className={'text-blue-100 font-medium mb-1'}>{t('server.timezone_info')}</p>
                                        <p className={'text-blue-200/80 text-xs mb-2'}>
                                            {t('server.timezone_server_desc')}
                                            {timezoneInfo.difference !== 'same time' && (
                                                <span className={'text-blue-100 font-medium'}>
                                                    {' '}
                                                    {t('server.timezone_diff', { difference: timezoneInfo.difference })}
                                                </span>
                                            )}
                                        </p>
                                        <div className={'mt-2 text-xs space-y-1'}>
                                            <div className={'text-blue-200/60'}>
                                                {t('server.your_timezone')}
                                                <span className={'font-mono'}>
                                                    {' '}
                                                    {formatTimezoneDisplay(
                                                        timezoneInfo.user.timezone,
                                                        timezoneInfo.user.offset,
                                                    )}
                                                </span>
                                            </div>
                                            <div className={'text-blue-200/60'}>
                                                {t('server.server_timezone')}
                                                <span className={'font-mono'}>
                                                    {' '}
                                                    {formatTimezoneDisplay(
                                                        timezoneInfo.server.timezone,
                                                        timezoneInfo.server.offset,
                                                    )}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        <div className='gap-3 my-6 flex flex-col'>
                            <a href='https://crontab.guru/' target='_blank' rel='noreferrer'>
                                <ItemContainer
                                    description={t('server.crontab_guru_desc')}
                                    title={t('server.crontab_guru')}
                                    // defaultChecked={showCheatsheet}
                                    // onChange={() => setShowCheetsheet((s) => !s)}
                                    labelClasses='cursor-pointer'
                                >
                                    <Link width={22} height={22} fill='currentColor' className={`px-5 h-5 w-5`} />
                                </ItemContainer>
                            </a>
                            {/* This table would be pretty awkward to make look nice
                            Maybe there could be an element for a dropdown later? */}
                            {/* {showCheatsheet && (
                            <div className={`block md:flex w-full`}>
                                <ScheduleCheatsheetCards />
                            </div>
                        )} */}
                            <FormikSwitchV2
                                name={'onlyWhenOnline'}
                                description={t('server.only_when_online')}
                                label={t('server.only_when_server_online')}
                            />
                            <FormikSwitchV2
                                name={'enabled'}
                                description={t('server.schedule_enabled_desc')}
                                label={t('server.schedule_enabled')}
                            />
                        </div>
                        <div className={`mb-6 text-right`}>
                            <ActionButton
                                variant='primary'
                                className={'w-full sm:w-auto'}
                                type={'submit'}
                                disabled={isSubmitting}
                            >
                                {schedule ? t('server.save_changes_btn') : t('server.create_schedule_btn')}
                            </ActionButton>
                        </div>
                    </Form>
                );
            }}
        </Formik>
    );
};

export default asModal<Props>()(EditScheduleModal);
