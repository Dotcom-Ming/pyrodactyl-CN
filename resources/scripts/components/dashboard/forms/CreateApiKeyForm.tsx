import { Actions, useStoreActions } from 'easy-peasy';
import { Field, Form, Formik, FormikHelpers } from 'formik';
import { useState } from 'react';
import { Fragment } from 'react';
import { object, string } from 'yup';

import FlashMessageRender from '@/components/FlashMessageRender';
import ApiKeyModal from '@/components/dashboard/ApiKeyModal';
import ActionButton from '@/components/elements/ActionButton';
import ContentBox from '@/components/elements/ContentBox';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import Input from '@/components/elements/Input';
import PageContentBlock from '@/components/elements/PageContentBlock';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';

import createApiKey from '@/api/account/createApiKey';
import { ApiKey } from '@/api/account/getApiKeys';
import { httpErrorToHuman } from '@/api/http';

import { t } from '@/lib/i18n';

import { ApplicationStore } from '@/state';

interface Values {
    description: string;
    allowedIps: string;
}

const CreateApiKeyForm = ({ onKeyCreated }: { onKeyCreated: (key: ApiKey) => void }) => {
    const [apiKey, setApiKey] = useState('');
    const { addError, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const submit = (values: Values, { setSubmitting, resetForm }: FormikHelpers<Values>) => {
        clearFlashes('account');
        createApiKey(values.description, values.allowedIps)
            .then(({ secretToken, ...key }) => {
                resetForm();
                setSubmitting(false);
                setApiKey(`${key.identifier}${secretToken}`);
                onKeyCreated(key);
            })
            .catch((error) => {
                console.error(error);

                addError({ key: 'account', message: httpErrorToHuman(error) });
                setSubmitting(false);
            });
    };

    return (
        <>
            {/* Flash Messages */}
            <FlashMessageRender byKey='account' />

            {/* Modal for API Key */}
            <ApiKeyModal visible={apiKey.length > 0} onModalDismissed={() => setApiKey('')} apiKey={apiKey} />

            {/* Form for creating API key */}
            <ContentBox>
                <Formik
                    onSubmit={submit}
                    initialValues={{ description: '', allowedIps: '' }}
                    validationSchema={object().shape({
                        allowedIps: string(),
                        description: string().required(t('server.description_required')).min(4),
                    })}
                >
                    {({ isSubmitting }) => (
                        <Form className='space-y-6'>
                            {/* Show spinner overlay when submitting */}
                            <SpinnerOverlay visible={isSubmitting} />

                            {/* Description Field */}
                            <FormikFieldWrapper
                                label={t('server.description')}
                                name='description'
                                description={t('server.description_desc')}
                            >
                                <Field name='description' as={Input} className='w-full' />
                            </FormikFieldWrapper>

                            {/* Allowed IPs Field */}
                            <FormikFieldWrapper
                                label={t('server.allowed_ips')}
                                name='allowedIps'
                                description={t('server.allowed_ips_desc')}
                            >
                                <Field name='allowedIps' as={Input} className='w-full' />
                            </FormikFieldWrapper>

                            {/* Submit Button below form fields */}
                            <div className='flex justify-end mt-6'>
                                <ActionButton type='submit' disabled={isSubmitting}>
                                    {isSubmitting ? t('strings.creating') : t('server.create_api_key')}
                                </ActionButton>
                            </div>
                        </Form>
                    )}
                </Formik>
            </ContentBox>
        </>
    );
};

CreateApiKeyForm.displayName = 'CreateApiKeyForm';
export default CreateApiKeyForm;
