const path = require('path');
const merge = require('webpack-merge');
const glob = require('glob');

const CleanObsoleteChunks = require('webpack-clean-obsolete-chunks');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');

const assets_path = path.join(__dirname, 'app', 'assets');

const common_config = merge([
{
  entry: {
    main: Array.prototype.concat(
      '@babel/polyfill',
      glob.sync(path.join(assets_path, 'javascripts', 'src', '*.js')),
      path.join(assets_path, 'stylesheets', 'scss', 'main.scss')
    )
  },
  output: {
    path: assets_path,
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: [path.join(__dirname, 'node_modules')],
        options: {
          presets: ['@babel/preset-env'],
          plugins: ['@babel/plugin-transform-runtime'],
        },
      },
      {
        test: /\.(sass|scss)$/,
        use: [
          'file-loader',
          'extract-loader',
          'css-loader?-url',
          'postcss-loader',
          'sass-loader'
        ]
      }
    ]
  },
  plugins: [
    new CleanObsoleteChunks({ verbose: false })
  ]
}]);

const development_config = merge([
{
  devtool: 'cheap-eval-source-map',
  stats: 'minimal',
  output: {
    filename: 'javascripts/compiled/[name].[hash].js',
  },
  watchOptions: {
    poll: true
  },
  module: {
    rules: [
      {
        test: /\.(sass|scss)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: 'stylesheets/compiled/[name].[hash].css',
            }
          },
        ]
      }
    ]
  }
}]);

const production_config = merge([
{
  stats: 'errors-only',
  output: {
    filename: 'javascripts/dist/[name].min.js',
  },
  module: {
    rules: [
      {
        test: /\.(sass|scss)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: 'stylesheets/dist/[name].css',
            }
          },
        ]
      }
    ]
  },
  plugins: [
    new OptimizeCSSAssetsPlugin(
      {
        cssProcessor: require('cssnano'),
        cssProcessorPluginOptions: {
          preset: ['default', { discardComments: { removeAll: true } }],
        },
      }
    ),
  ]
}]);

module.exports = (env, argv) => {
  var mode = argv.mode;

  switch (mode)
  {
    case 'development':
      return merge.smart({ mode }, common_config, development_config);

    case 'production':
    default:
      return merge.smart({ mode }, common_config, production_config);
  }
};
