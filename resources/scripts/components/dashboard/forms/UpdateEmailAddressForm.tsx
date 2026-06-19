import { Actions, State, useStoreActions, useStoreState } from 'easy-peasy';
import { Form, Formik, FormikHelpers } from 'formik';
import { Fragment } from 'react';
import * as Yup from 'yup';

import ActionButton from '@/components/elements/ActionButton';
import Field from '@/components/elements/Field';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';

import { t } from '@/lib/i18n';

import { httpErrorToHuman } from '@/api/http';

import { ApplicationStore } from '@/state';

interface Values {
    email: string;
    password: string;
}

const UpdateEmailAddressForm = () => {
    const user = useStoreState((state: State<ApplicationStore>) => state.user.data);
    const updateEmail = useStoreActions((state: Actions<ApplicationStore>) => state.user.updateUserEmail);

    const { clearFlashes, addFlash } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const schema = Yup.object().shape({
        email: Yup.string().email(t('auth.validation_email_valid')).required(t('auth.validation_email_required')),
        password: Yup.string().required(t('strings.current_account_password_required')),
    });

    const submit = (values: Values, { resetForm, setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('account:email');

        updateEmail({ ...values })
            .then(() =>
                addFlash({
                    type: 'success',
                    key: 'account:email',
                    message: t('dashboard.account.email.updated'),
                }),
            )
            .catch((error) =>
                addFlash({
                    type: 'error',
                    key: 'account:email',
                    title: t('strings.error'),
                    message: httpErrorToHuman(error),
                }),
            )
            .then(() => {
                resetForm();
                setSubmitting(false);
            });
    };

    return (
        <Formik onSubmit={submit} validationSchema={schema} initialValues={{ email: user!.email, password: '' }}>
            {({ isSubmitting, isValid }) => (
                <Fragment>
                    <SpinnerOverlay size={'large'} visible={isSubmitting} />
                    <Form className={`m-0`}>
                        <Field id={'current_email'} type={'email'} name={'email'} label={t('strings.email')} />
                        <div className={`mt-6`}>
                            <Field id={'confirm_password'} type={'password'} name={'password'} label={t('strings.password')} />
                        </div>
                        <div className={`mt-6`}>
                            <ActionButton variant='primary' disabled={isSubmitting || !isValid}>
                                {t('strings.update_email')}
                            </ActionButton>
                        </div>
                    </Form>
                </Fragment>
            )}
        </Formik>
    );
};

export default UpdateEmailAddressForm;
