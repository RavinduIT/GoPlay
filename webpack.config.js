const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';

    return {
        entry: {
            // Main application bundles
            app: './resources/js/app.js',
            admin: './resources/js/admin.js',
            
            // Page-specific bundles
            'pages/home': './resources/js/controllers/index.js',
            'pages/facilities': './resources/js/controllers/book-ground.js',
            'pages/facility-details': './resources/js/controllers/ground-details.js',
            'pages/coaches': './resources/js/controllers/book-coach.js',
            'pages/shop': './resources/js/controllers/shop.js',
            'pages/cart': './resources/js/controllers/cart.js',
            'pages/profile': './resources/js/controllers/profile.js',
            'pages/auth': './resources/js/controllers/login.js',
            
            // Vendor libraries
            vendor: ['axios', 'chart.js', 'leaflet', 'swiper']
        },

        output: {
            path: path.resolve(__dirname, 'public/assets'),
            filename: isProduction ? 'js/[name].[contenthash].js' : 'js/[name].js',
            chunkFilename: isProduction ? 'js/[name].[contenthash].js' : 'js/[name].js',
            publicPath: '/assets/',
            clean: true
        },

        module: {
            rules: [
                // JavaScript files
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env']
                        }
                    }
                },

                // CSS and SCSS files
                {
                    test: /\.(css|scss|sass)$/,
                    use: [
                        isProduction ? MiniCssExtractPlugin.loader : 'style-loader',
                        'css-loader',
                        'sass-loader'
                    ]
                },

                // Images
                {
                    test: /\.(png|jpe?g|gif|svg)$/i,
                    type: 'asset',
                    parser: {
                        dataUrlCondition: {
                            maxSize: 8 * 1024 // 8kb
                        }
                    },
                    generator: {
                        filename: 'images/[name].[hash][ext]'
                    }
                },

                // Fonts
                {
                    test: /\.(woff|woff2|eot|ttf|otf)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: 'fonts/[name].[hash][ext]'
                    }
                }
            ]
        },

        plugins: [
            new MiniCssExtractPlugin({
                filename: isProduction ? 'css/[name].[contenthash].css' : 'css/[name].css',
                chunkFilename: isProduction ? 'css/[name].[contenthash].css' : 'css/[name].css'
            }),

            // Generate manifest for cache busting
            new (require('webpack-manifest-plugin').WebpackManifestPlugin)({
                fileName: 'manifest.json',
                publicPath: '/assets/'
            })
        ],

        optimization: {
            splitChunks: {
                chunks: 'all',
                cacheGroups: {
                    vendor: {
                        test: /[\\/]node_modules[\\/]/,
                        name: 'vendors',
                        chunks: 'all'
                    },
                    common: {
                        name: 'common',
                        minChunks: 2,
                        priority: 10,
                        reuseExistingChunk: true
                    }
                }
            }
        },

        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources'),
                '@js': path.resolve(__dirname, 'resources/js'),
                '@css': path.resolve(__dirname, 'resources/css'),
                '@components': path.resolve(__dirname, 'resources/js/components'),
                '@controllers': path.resolve(__dirname, 'resources/js/controllers'),
                '@models': path.resolve(__dirname, 'resources/js/models'),
                '@services': path.resolve(__dirname, 'resources/js/services'),
                '@utils': path.resolve(__dirname, 'resources/js/utils')
            },
            extensions: ['.js', '.json', '.css', '.scss']
        },

        devServer: {
            static: {
                directory: path.join(__dirname, 'public')
            },
            compress: true,
            port: 3000,
            hot: true,
            open: true,
            proxy: {
                '/api': {
                    target: 'http://localhost:8000',
                    changeOrigin: true
                }
            }
        },

        devtool: isProduction ? 'source-map' : 'eval-source-map',

        performance: {
            hints: isProduction ? 'warning' : false,
            maxEntrypointSize: 512000,
            maxAssetSize: 512000
        }
    };
};