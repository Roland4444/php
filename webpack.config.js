const webpack = require('webpack');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const path = require('path');

const entry = () => {
    const scriptFiles = [
        'finance/metal-expenses',
        'finance/other-expenses',
        'finance/other-receipts',
        'finance/trader-receipts',
        'finance/cash-transfer',
        'finance/money-department',
        'finance/total',
        'shipmentdoc/receiver',
        'shipmentdoc/owner',
        'shipmentdoc/driver',
        'shipmentdoc/document',
        'reports/weighing',
        'reports/export',
        'reports/limits',
        'spare/spare',
        'spare/inventory',
        'spare/payment',
        'spare/consumption',
        'spare/transfer',
        'spare/receipt',
        'spare/order',
        'spare/planning',
        'storage/purchase',
        'storage/shipment',
        'storage/cash-total-legal',
        'factoring/payment',
        'spare/seller-returns',
        'transport/income',
        'office_cash/transport_income',
    ];

    const entryFiles = {};
    scriptFiles.forEach((item) => {
        let fileName = item.replace(/[/-]/g, '_');
        entryFiles[fileName] = './resources/assets/js/' + item + '/index.js'
    });

    entryFiles['bundle'] = './resources/assets/js/app.js';

    return entryFiles;
};

module.exports = {
    entry: entry(),
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, "public")
    },
    /*optimization: {
        splitChunks: {
            chunks: 'initial'
        }
    },*/
    //devtool: 'source-map',
    module: {
        rules: [
            {
                test: /\.js$/,
                loader: "babel-loader",
                exclude: /(node_modules|bower_components)/
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.css$/,
                loader: "style-loader!css-loader"
            },
            {
                test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
                use: [{
                    loader: "file-loader",
                    options: {
                        name: '[name].[ext]',
                        outputPath: 'fonts/'
                    }
                }]
            },
            {
                test: /\.(png|gif|ico)$/,
                use: [{
                    loader: "file-loader",
                    options: {
                        name: '[name].[ext]',
                        outputPath: '/images/'
                    }
                }]
            }
        ]
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
        }),
        new VueLoaderPlugin()
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue'
        }
    }
};
