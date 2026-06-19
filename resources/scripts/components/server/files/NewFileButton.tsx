import { NavLink } from 'react-router-dom';

import ActionButton from '@/components/elements/ActionButton';
import { t } from '@/lib/i18n';

const NewFileButton = ({ id }: { id: string }) => {
    return (
        <NavLink to={`/server/${id}/files/new${window.location.hash}`}>
            <ActionButton variant='secondary' size='md'>
                {t('strings.new_file')}
            </ActionButton>
        </NavLink>
    );
};

export default NewFileButton;
