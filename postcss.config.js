export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
        // Production'da CSS optimizasyonu
        ...(process.env.NODE_ENV === 'production' ? {
            cssnano: {
                preset: ['default', {
                    discardComments: {
                        removeAll: true,
                    },
                    normalizeWhitespace: true,
                    colormin: true,
                    minifyFontValues: true,
                    minifySelectors: true,
                }]
            }
        } : {})
    },
};
