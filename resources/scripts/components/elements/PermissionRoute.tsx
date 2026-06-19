import type { JSX, ReactNode } from 'react';

import { ServerError } from '@/components/elements/ScreenBlock';

import { t } from '@/lib/i18n';

import { usePermissions } from '@/plugins/usePermissions';

interface Props {
    children?: ReactNode;
    permission?: string | string[];
}

function PermissionRoute({ children, permission }: Props): JSX.Element {
    const can = usePermissions(permission || []);

    if (permission === undefined || permission === null) {
        return <>{children}</>;
    }

    if (can.filter((p) => p).length > 0) {
        return <>{children}</>;
    }

    return <ServerError title={t('strings.access_denied')} message={t('strings.access_denied_desc')} />;
}

export default PermissionRoute;
