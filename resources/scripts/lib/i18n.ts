type TranslationMap = Record<string, string | Record<string, unknown>>;

let enCache: Record<string, TranslationMap> = {};
let localeCache: Record<string, TranslationMap> = {};
let currentLocale = 'en';

export function getLocale(): string {
    if (typeof window !== 'undefined' && (window as any).SiteConfiguration?.locale) {
        return (window as any).SiteConfiguration.locale;
    }
    return currentLocale;
}

export async function loadTranslations(namespaces: string[]): Promise<void> {
    const locale = getLocale();
    currentLocale = locale;

    const toLoad = namespaces.filter((ns) => !localeCache[ns]);
    if (toLoad.length === 0) return;

    // Always load English as base fallback, then overlay target locale
    const [enResults, localeResults] = await Promise.all([
        Promise.all(
            toLoad.map(async (namespace) => {
                try {
                    const res = await fetch(`/locales/locale.json?locale=en&namespace=${namespace}`);
                    const data = await res.json();
                    return { namespace, data: data['en']?.[namespace] ?? null };
                } catch {
                    return { namespace, data: null };
                }
            }),
        ),
        locale !== 'en'
            ? Promise.all(
                  toLoad.map(async (namespace) => {
                      try {
                          const res = await fetch(`/locales/locale.json?locale=${locale}&namespace=${namespace}`);
                          const data = await res.json();
                          return { namespace, data: data[locale]?.[namespace] ?? null };
                      } catch {
                          return { namespace, data: null };
                      }
                  }),
              )
            : Promise.resolve(toLoad.map((ns) => ({ namespace: ns, data: null }))),
    ]);

    for (const { namespace, data } of enResults) {
        if (data) enCache[namespace] = data;
    }

    for (const { namespace, data } of localeResults) {
        if (data) {
            localeCache[namespace] = data;
        } else {
            // Use English as fallback for this namespace
            localeCache[namespace] = enCache[namespace] ?? {};
        }
    }
}

function lookup(map: TranslationMap, path: string): string | undefined {
    let current: any = map;
    for (const part of path.split('.')) {
        if (current && typeof current === 'object' && part in current) {
            current = current[part];
        } else {
            return undefined;
        }
    }
    return typeof current === 'string' ? current : undefined;
}

function resolveNamespace(key: string): { namespace: string; keyPath: string } | undefined {
    const namespaces = Array.from(new Set([...Object.keys(localeCache), ...Object.keys(enCache)])).sort(
        (a, b) => b.length - a.length,
    );

    for (const namespace of namespaces) {
        const prefix = `${namespace.replace(/\//g, '.')}.`;
        if (key.startsWith(prefix)) {
            return { namespace, keyPath: key.substring(prefix.length) };
        }
    }

    const dotIndex = key.indexOf('.');
    if (dotIndex === -1) return undefined;

    return {
        namespace: key.substring(0, dotIndex),
        keyPath: key.substring(dotIndex + 1),
    };
}

export function t(key: string, params?: Record<string, string | number>): string {
    const resolved = resolveNamespace(key);
    if (!resolved) return key;

    const { namespace, keyPath } = resolved;

    // Try target locale first, fallback to English
    let value: string | undefined;
    if (localeCache[namespace]) {
        value = lookup(localeCache[namespace], keyPath);
    }
    if (value === undefined && enCache[namespace]) {
        value = lookup(enCache[namespace], keyPath);
    }
    if (value === undefined) return key;

    if (params) {
        return value
            .replace(/:(\w+)/g, (_, p: string) => String(params[p] ?? `:${p}`))
            .replace(/\{\{([\w.-]+)\}\}/g, (_, p: string) => String(params[p] ?? `{{${p}}}`));
    }
    return value;
}
