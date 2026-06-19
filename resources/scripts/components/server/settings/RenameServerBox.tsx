import { Actions, useStoreActions } from 'easy-peasy';
import { Form, Formik } from 'formik';
import { toast } from 'sonner';
import { object, string } from 'yup';

import ActionButton from '@/components/elements/ActionButton';
import Field from '@/components/elements/Field';
import TitledGreyBox from '@/components/elements/TitledGreyBox';

import { httpErrorToHuman } from '@/api/http';
import renameServer from '@/api/server/renameServer';
import { t } from '@/lib/i18n';

import { ApplicationStore } from '@/state';
import { ServerContext } from '@/state/server';

interface Values {
    name: string;
    description: string;
}

const RenameServerForm = () => {
    return (
        <TitledGreyBox title={t('strings.server_details')}>
            <Form className='flex flex-col gap-4'>
                <Field id={'name'} name={'name'} label={t('strings.server_name_label')} type={'text'} />
                <Field id={'description'} name={'description'} label={t('strings.server_desc_label')} type={'text'} />
                <div className={`mt-6 text-right`}>
                    <ActionButton variant='primary' type={'submit'}>
                        {t('strings.save')}
                    </ActionButton>
                </div>
            </Form>
        </TitledGreyBox>
    );
};

const RenameServerBox = () => {
    const server = ServerContext.useStoreState((state) => state.server.data!);
    const setServer = ServerContext.useStoreActions((actions) => actions.server.setServer);
    const { addError, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const submit = ({ name, description }: Values) => {
        clearFlashes('settings');
        toast(t('strings.updating_server'));
        renameServer(server.uuid, name, description)
            .then(() => setServer({ ...server, name, description }))
            .catch((error) => {
                console.error(error);
                addError({ key: 'settings', message: httpErrorToHuman(error) });
            })
            .then(() => toast.success(t('strings.server_updated')));
    };

    return (
        <Formik
            onSubmit={submit}
            initialValues={{
                name: server.name,
                description: server.description,
            }}
            validationSchema={object().shape({
                name: string().required().min(1),
                description: string().nullable(),
            })}
        >
            <RenameServerForm />
        </Formik>
    );
};

export default RenameServerBox;
