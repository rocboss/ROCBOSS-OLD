var webpack = require('webpack')
var path = require('path');
var glob = require('glob');
var moment = require("moment");
var ExtractTextPlugin = require("extract-text-webpack-plugin");
var node_modules_dir = path.resolve(__dirname, 'node_modules');

// 判断生产&&测试环境
var isProduction = function () {
    return process.env.NODE_ENV === 'production';
};

// 判断开发(热加载)环境
var isHot = function () {
    return process.env.NODE_ENV === 'hotdev';
};

// 不同环境输出到不同文件夹
var sEnvironment = function() {
    console.log(process.env.NODE_ENV);
    switch (process.env.NODE_ENV) {
        case 'hotdev':
            return 'web/hot/';

        case 'production':
            return 'web/dist/';

        default:
            return 'web/dev/';
    }
};

function getEntry() {
    var entry = {};
    var srcDirName = './app/views/js/*.js';
    glob.sync(srcDirName).forEach(function (name) {
        n = name.slice(name.lastIndexOf('/') + 1, -3);
        if (n != 'common') {
            entry[n] = name;
        }
    });
    console.log('是否压缩文件：'+isProduction());
    console.log('输出路径：'+sEnvironment());
    return entry;
}

var packCSS = new ExtractTextPlugin('./css/[name].min.css', {
    allChunks: true
});

var config = {
  entry:  getEntry(),
  output: {
    path: path.resolve(__dirname, sEnvironment()),
    publicPath: isHot() ? sEnvironment() : './',
    filename:'[name].min.js',
    chunkFilename: '[name].[chunkhash:8].js'
  },
  module: {
    loaders: [{
        test: /\.js$/,
        exclude: [node_modules_dir],
        loader: 'babel',
        query: {
            presets: ['es2015'],
            "ignore": [
                "webuploader"
            ]
        }
    }, {
        test: /\.css$/,
        loader:ExtractTextPlugin.extract('style-loader', 'css-loader')
    }, {
        test: /\.(gif|jpg|png|woff|svg|eot|ttf)\??.*$/,
        loader: 'url-loader?limit=512000&name=[path][name].[ext]'
    }, {
        test: require.resolve('jquery'),
        loader: 'expose?jQuery!expose?$'
    }]
  },
  devtool: 'eval',
  resolve: {
    extensions: ['', '.js', '.jsx', '.less', '.css'],
    root: [
        path.resolve('./app/views')
    ],
    alias: {
        "bootstrap": "vendor/bootstrap.min",
        "lazyload": "vendor/jquery.lazyload.min.js",
        "webuploader": "vendor/webuploader/webuploader.js",
        "layer": "vendor/layer/layer",
        "laypage": "vendor/laypage",
        "vue": "vendor/vue.min",
        "fancybox": "vendor/jquery.fancybox",
        "wangEditor": "vendor/wangEditor.js",
        "peity": "vendor/jquery.peity.min",
        "vue": "vendor/vue.min",
        "laypage": "vendor/laypage",
        "highlight": "vendor/highlight.pack"
    },
  },
  externals: {

  },
  plugins: [
    packCSS,
    new webpack.optimize.UglifyJsPlugin({
        compress: {
            warnings: false
        },
        mangle: {
            except: ['$', 'webpackJsonpCallback']
        }
    }),
    new webpack.ProvidePlugin({
        jQuery: "jquery",
        $: "jquery",
        "window.jQuery": "jquery"
    }),
    new webpack.BannerPlugin("Author: ROC\nWebpack Packing Date: " + moment().format("YYYY-MM-DD HH:mm:ss"))
  ]
};

module.exports = config;
