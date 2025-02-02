const Encore = require('@symfony/webpack-encore');
const path = require('path');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore

    .setOutputPath('public/build/')

    .setPublicPath('/build')

    .addEntry('app', './assets/app.js')

    .splitEntryChunks()

    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())

    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-syntax-dynamic-import');
    })

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    .enableTypeScriptLoader(options => {
        options.transpileOnly = true;
        options.configFile = path.resolve(__dirname, 'tsconfig.json');
    })

    .enableReactPreset()

    .addAliases({
        '@': path.resolve(__dirname, 'assets'),
        '@backoffice': path.resolve(__dirname, 'assets/backoffice'),
        '@frontoffice': path.resolve(__dirname, 'assets/frontoffice'),
        '@shared': path.resolve(__dirname, 'assets/shared'),
    })

    .enableTypeScriptLoader(options => {
        options.transpileOnly = true;
        options.configFile = path.resolve(__dirname, 'tsconfig.json');
    })

    .enableReactPreset()

    .addRule({
        test: /\.(ts|tsx|js|jsx)$/,
        include: path.resolve(__dirname, 'assets'),
        exclude: /node_modules/,
        resolve: {
            extensions: ['.tsx', '.ts', '.js', '.jsx', '.json', '.css']
        }
    })

    .configureDevServerOptions(options => {
        if (!options.static) {
            options.static = [];
        } else if (!Array.isArray(options.static)) {
            options.static = [options.static];
        }

        options.liveReload = true;
        options.hot = true;

        options.historyApiFallback = {
            rewrites: [
                {
                    from: /^\/admin.*$/,
                    to: '/admin/index.html'
                },
                {
                    from: /./,
                    to: '/index.html'
                }
            ]
        };

        options.headers = {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
            'Access-Control-Allow-Headers': 'X-Requested-With, content-type, Authorization'
        };
    })

    .enablePostCssLoader()

    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
        pattern: /\.(png|jpg|jpeg|gif|ico|svg|webp)$/
    })
    .copyFiles({
        from: './assets/fonts',
        to: 'fonts/[path][name].[hash:8].[ext]',
        pattern: /\.(woff|woff2|eot|ttf|otf)$/
    })

    .configureSplitChunks(splitChunks => {
        splitChunks.cacheGroups = {
            vendors: {
                test: /[\\/]node_modules[\\/]/,
                priority: -10,
                name: 'vendors',
                chunks: 'all'
            },
            common: {
                minChunks: 2,
                priority: -20,
                chunks: 'all',
                name: 'common'
            }
        };
    })
;

module.exports = Encore.getWebpackConfig();