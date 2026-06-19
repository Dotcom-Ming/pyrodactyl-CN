import Can from '@/components/elements/Can';
import { ServerError } from '@/components/elements/ScreenBlock';
import { t } from '@/lib/i18n';

export interface RequireServerPermissionProps {
    permissions: string | string[];
    children?: React.ReactNode;
}

const RequireServerPermission: React.FC<RequireServerPermissionProps> = ({ children, permissions }) => {
    return (
        <Can
            action={permissions}
            renderOnError={
                <ServerError title={t('strings.access_denied')} message={t('strings.access_denied_desc')} />
            }
        >
            {children}
        </Can>
    );
};

export default RequireServerPermission;
