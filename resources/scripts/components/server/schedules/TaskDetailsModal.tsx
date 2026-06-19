import ModalContext from '@/context/ModalContext';
import { Form, Formik, Field as FormikField, FormikHelpers, useField } from 'formik';
import { useContext, useEffect } from 'react';
import styled from 'styled-components';
import { boolean, number, object, string } from 'yup';

import FlashMessageRender from '@/components/FlashMessageRender';
import ActionButton from '@/components/elements/ActionButton';
import Field from '@/components/elements/Field';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import FormikSwitchV2 from '@/components/elements/FormikSwitchV2';
import { Textarea } from '@/components/elements/Input';
import Select from '@/components/elements/Select';

import asModal from '@/hoc/asModal';

import { httpErrorToHuman } from '@/api/http';
import createOrUpdateScheduleTask from '@/api/server/schedules/createOrUpdateScheduleTask';
import { Schedule, Task } from '@/api/server/schedules/getServerSchedules';

import { ServerContext } from '@/state/server';

import useFlash from '@/plugins/useFlash';
import { t } from '@/lib/i18n';

// TODO: Port modern dropdowns to Formik and integrate them
// import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem } from '@/components/elements/DropdownMenu';
// import { DropdownMenuTrigger } from '@radix-ui/react-dropdown-menu';

const Label = styled.label`
    display: inline-block;
    color: #ffffff77;
    font-size: 0.875rem;
    padding-bottom: 0.5rem;
`;

interface Props {
    schedule: Schedule;
    // If a task is provided we can assume we're editing it. If not provided,
    // we are creating a new one.
    task?: Task;
}

interface Values {
    action: string;
    payload: string;
    timeOffset: string;
    continueOnFailure: boolean;
}

const getSchema = () =>
    object().shape({
        action: string().required().oneOf(['command', 'power', 'backup']),
        payload: string().when('action', {
            is: (v) => v !== 'backup',
            then: () => string().required(t('server.validation_task_payload_required')),
            otherwise: () => string(),
        }),
        continueOnFailure: boolean(),
        timeOffset: number()
            .typeError(t('server.validation_time_offset_number'))
            .required(t('server.validation_time_offset_required'))
            .min(0, t('server.validation_time_offset_min'))
            .max(900, t('server.validation_time_offset_max')),
    });

const ActionListener = () => {
    const [{ value }, { initialValue: initialAction }] = useField<string>('action');
    const [, { initialValue: initialPayload }, { setValue, setTouched }] = useField<string>('payload');

    useEffect(() => {
        if (value !== initialAction) {
            setValue(value === 'power' ? 'start' : '');
            setTouched(false);
        } else {
            setValue(initialPayload || '');
            setTouched(false);
        }
    }, [value]);

    return null;
};

const TaskDetailsModal = ({ schedule, task }: Props) => {
    const { dismiss, setPropOverrides } = useContext(ModalContext);
    const { clearFlashes, addError } = useFlash();

    const uuid = ServerContext.useStoreState((state) => state.server.data!.uuid);
    const appendSchedule = ServerContext.useStoreActions((actions) => actions.schedules.appendSchedule);
    const backupLimit = ServerContext.useStoreState((state) => state.server.data!.featureLimits.backups);

    useEffect(() => {
        return () => {
            clearFlashes('schedule:task');
        };
    }, []);

    useEffect(() => {
        setPropOverrides({ title: task ? t('server.edit_task') : t('server.create_task') });
    }, []);

    const submit = (values: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('schedule:task');
        if (backupLimit === 0 && values.action === 'backup') {
            setSubmitting(false);
            addError({
                message: t('server.backup_task_limit_zero'),
                key: 'schedule:task',
            });
        } else {
            createOrUpdateScheduleTask(uuid, schedule.id, task?.id, values)
                .then((task) => {
                    let tasks = schedule.tasks.map((t) => (t.id === task.id ? task : t));
                    if (!schedule.tasks.find((t) => t.id === task.id)) {
                        tasks = [...tasks, task];
                    }

                    appendSchedule({ ...schedule, tasks });
                    dismiss();
                })
                .catch((error) => {
                    console.error(error);
                    setSubmitting(false);
                    addError({ message: httpErrorToHuman(error), key: 'schedule:task' });
                });
        }
    };

    return (
        <div className='min-w-full'>
            <Formik
                onSubmit={submit}
                validationSchema={getSchema()}
                initialValues={{
                    action: task?.action || 'command',
                    payload: task?.payload || '',
                    timeOffset: task?.timeOffset.toString() || '0',
                    continueOnFailure: task?.continueOnFailure || false,
                }}
            >
                {({ isSubmitting, values }) => (
                    <Form>
                        <FlashMessageRender byKey={'schedule:task'} />
                        <div className={`flex flex-col gap-3`}>
                            <div>
                                <Label>{t('server.action_label')}</Label>
                                <ActionListener />
                                <FormikFieldWrapper name={'action'}>
                                    <FormikField
                                        className='px-4 py-2 bg-[#ffffff11] rounded-lg min-w-full'
                                        as={Select}
                                        name={'action'}
                                    >
                                        <option className='bg-black' value={'command'}>
                                            {t('server.send_command_option')}
                                        </option>
                                        <option className='bg-black' value={'power'}>
                                            {t('server.power_option')}
                                        </option>
                                        <option className='bg-black' value={'backup'}>
                                            {t('server.create_backup_option')}
                                        </option>
                                    </FormikField>
                                </FormikFieldWrapper>
                            </div>
                            <div>
                                <Field
                                    name={'timeOffset'}
                                    label={t('server.time_offset')}
                                    description={t('server.time_offset_desc')}
                                />
                            </div>
                        </div>
                        <div className={`my-6`}>
                            {values.action === 'command' ? (
                                <div>
                                    <Label>{t('server.payload')}</Label>
                                    <FormikFieldWrapper name={'payload'}>
                                        <FormikField
                                            className='w-full rounded-xl p-2 bg-[#ffffff11]'
                                            as={Textarea}
                                            name={'payload'}
                                            rows={6}
                                        />
                                    </FormikFieldWrapper>
                                </div>
                            ) : values.action === 'power' ? (
                                <div>
                                    <Label>{t('server.payload')}</Label>
                                    <FormikFieldWrapper name={'payload'}>
                                        <FormikField
                                            className='px-4 py-2 bg-[#ffffff11] rounded-lg min-w-full'
                                            as={Select}
                                            name={'payload'}
                                        >
                                            <option className='bg-black' value={'start'}>
                                                {t('server.start_server')}
                                            </option>
                                            <option className='bg-black' value={'restart'}>
                                                {t('server.restart_server')}
                                            </option>
                                            <option className='bg-black' value={'stop'}>
                                                {t('server.stop_server')}
                                            </option>
                                            <option className='bg-black' value={'kill'}>
                                                {t('server.terminate_server')}
                                            </option>
                                        </FormikField>
                                    </FormikFieldWrapper>
                                </div>
                            ) : (
                                <div>
                                    <Label>{t('server.ignored_files_optional')}</Label>
                                    <FormikFieldWrapper
                                        name={'payload'}
                                        description={t('server.ignored_files_desc')}
                                    >
                                        <FormikField
                                            className='w-full rounded-2xl bg-[#ffffff11]'
                                            as={Textarea}
                                            name={'payload'}
                                            rows={6}
                                        />
                                    </FormikFieldWrapper>
                                </div>
                            )}
                        </div>
                        <FormikSwitchV2
                            name={'continueOnFailure'}
                            description={t('server.continue_on_failure_desc')}
                            label={t('server.continue_on_failure')}
                        />
                        <div className={`flex justify-end my-6`}>
                            <ActionButton variant='primary' type={'submit'} disabled={isSubmitting}>
                                {task ? t('server.save_changes') : t('server.create_task')}
                            </ActionButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </div>
    );
};

export default asModal<Props>()(TaskDetailsModal);
