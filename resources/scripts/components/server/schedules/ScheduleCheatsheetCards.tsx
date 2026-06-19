import { t } from '@/lib/i18n';

const ScheduleCheatsheetCards = () => {
    return (
        <>
            <div className={`md:w-1/2 h-full bg-zinc-600`}>
                <div className={`flex flex-col`}>
                    <h2 className={`py-4 px-6 font-bold`}>{t('server.cron_examples')}</h2>
                    <div className={`flex py-4 px-6 bg-zinc-500`}>
                        <div className={`w-1/2`}>*/5 * * * *</div>
                        <div className={`w-1/2`}>{t('server.every_5_minutes')}</div>
                    </div>
                    <div className={`flex py-4 px-6`}>
                        <div className={`w-1/2`}>0 */1 * * *</div>
                        <div className={`w-1/2`}>{t('server.every_hour')}</div>
                    </div>
                    <div className={`flex py-4 px-6 bg-zinc-500`}>
                        <div className={`w-1/2`}>0 8-12 * * *</div>
                        <div className={`w-1/2`}>{t('server.hour_range')}</div>
                    </div>
                    <div className={`flex py-4 px-6`}>
                        <div className={`w-1/2`}>0 0 * * *</div>
                        <div className={`w-1/2`}>{t('server.once_a_day')}</div>
                    </div>
                    <div className={`flex py-4 px-6 bg-zinc-500`}>
                        <div className={`w-1/2`}>0 0 * * MON</div>
                        <div className={`w-1/2`}>{t('server.every_monday')}</div>
                    </div>
                </div>
            </div>
            <div className={`md:w-1/2 h-full bg-zinc-600`}>
                <h2 className={`py-4 px-6 font-bold`}>{t('server.special_characters')}</h2>
                <div className={`flex flex-col`}>
                    <div className={`flex py-4 px-6 bg-zinc-500`}>
                        <div className={`w-1/2`}>*</div>
                        <div className={`w-1/2`}>{t('server.any_value')}</div>
                    </div>
                    <div className={`flex py-4 px-6`}>
                        <div className={`w-1/2`}>,</div>
                        <div className={`w-1/2`}>{t('server.value_list_separator')}</div>
                    </div>
                    <div className={`flex py-4 px-6 bg-zinc-500`}>
                        <div className={`w-1/2`}>-</div>
                        <div className={`w-1/2`}>{t('server.range_values')}</div>
                    </div>
                    <div className={`flex py-4 px-6`}>
                        <div className={`w-1/2`}>/</div>
                        <div className={`w-1/2`}>{t('server.step_values')}</div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ScheduleCheatsheetCards;
