const path = require('path');
const fs = require('fs');

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const BeautifyHtmlWebpackPlugin = require('html-beautify-webpack-plugin');
const SvgStore = require('webpack-svgstore-plugin');

const LAYOUT_PATH = 'local/templates/le_journal/layout';

const PATHS = {
  src: path.join(__dirname, `../${LAYOUT_PATH}/src`),
  dist: path.join(__dirname, `../${LAYOUT_PATH}/dist`),
  assets: 'assets/'
};

const PAGES_DIR = `${PATHS.src}/pages/`;
const PAGES = fs
  .readdirSync(PAGES_DIR)
  .filter(fileName => fileName.endsWith('.pug'));

const MODALS_DIR = `${PATHS.src}/modals/`;
const MODALS = fs
  .readdirSync(MODALS_DIR)
  .filter(fileName => fileName.endsWith('.pug'));

const beautifyOptions = new BeautifyHtmlWebpackPlugin({
  "indent_size": 2,
  "indent_char": ` `,
  "indent_with_tabs": false,
  "editorconfig": false,
  "eol": `\n`,
  "end_with_newline": false,
  "indent_level": 0,
  "preserve_newlines": true,
  "max_preserve_newlines": 2,
  "space_in_paren": false,
  "space_in_empty_paren": false,
  "jslint_happy": false,
  "space_after_anon_function": false,
  "space_after_named_function": false,
  "brace_style": `collapse`,
  "unindent_chained_methods": false,
  "break_chained_methods": false,
  "keep_array_indentation": false,
  "unescape_strings": false,
  "wrap_line_length": 0,
  "e4x": false,
  "comma_first": false,
  "operator_position": `before-newline`,
  "indent_empty_lines": false,
  "templating": [`auto`],
})

module.exports = {
  externals: {
    paths: PATHS
  },
  entry: {
    main: PATHS.src
    // module: `${PATHS.src}/your-module.js`,
  },
  output: {
    filename: `${PATHS.assets}js/[name].js`,
    path: PATHS.dist,
    publicPath: '/'
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        vendor: {
          name: 'vendors',
          test: /node_modules/,
          chunks: 'all',
          enforce: true
        }
      }
    }
  },
  module: {
    rules: [
      {
        // JavaScript
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: '/node_modules/'
      },
      {
        // Vue
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loader: {
            sass: 'vue-style-loader!css-loader!sass-loader'
          }
        }
      },
      {
        // Fonts
        test: /\.(woff(2)?|ttf|eot)(\?v=\d+\.\d+\.\d+)?$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]',
          outputPath: `/assets/fonts`,
          publicPath: '../fonts'
        }
      },
      {
        // Images svg
        test: /\.(svg)(\?v=\d+\.\d+\.\d+)?$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]',
          outputPath: `/assets/img/svg`,
          publicPath: '../img/svg'
        }
      },
      {
        // Images / icons
        test: /\.(png|jpg|gif|svg)$/,
        loader: 'file-loader',
        exclude: [`${PATHS.src}/${PATHS.assets}sprite`],
        options: {
          name: '[name].[ext]'
        }
      },
      {
        // Sass
        test: /\.sass$/,
        use: [
          'style-loader',
          MiniCssExtractPlugin.loader,
          // publicPath: 'assets/css'
          {
            loader: 'css-loader',
            options: { sourceMap: true }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
              config: { path: `./postcss.config.js` }
            }
          },
          {
            loader: 'sass-loader',
            options: { sourceMap: true }
          }
        ]
      },
      {
        // css
        test: /\.css$/,
        use: [
          'style-loader',
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: { sourceMap: true }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
              config: { path: `./postcss.config.js` }
            }
          }
        ]
      },
      {
        // Pug
        test: /\.pug$/,
        use: ['html-loader?attributes=false', 'pug-html-loader']
      }
    ]
  },
  resolve: {
    alias: {
      '~': PATHS.src,
      utils: `${PATHS.src}/js/helpers/utils`,
      vue$: 'vue/dist/vue.js'
    }
  },
  plugins: [
    new SvgStore({
      svgoOptions: {
        plugins: [
          {
            removeAttrs: {
              attrs: ['width', 'height']
            }
          },
          { removeTitle: true },
          { convertColors: { currentColor: true } }
        ]
      },
      prefix: 'i-'
    }),
    // Provide utils
    new webpack.ProvidePlugin({
      $: 'utils'
    }),
    // Vue
    new VueLoaderPlugin(),
    // CSS
    new MiniCssExtractPlugin({
      filename: `${PATHS.assets}css/[name].css`
    }),
    // Copy files
    new CopyWebpackPlugin([
      { from: `${PATHS.src}/${PATHS.assets}img`, to: `${PATHS.assets}img` },
      { from: `${PATHS.src}/static`, to: '' }
    ]),

    // PUG pages
    ...PAGES.map(
      page =>
        new HtmlWebpackPlugin({
          template: `${PAGES_DIR}/${page}`,
          filename: `./${page.replace(/\.pug/, '.html')}`
        }),
    ),
    beautifyOptions,

    // PUG modals
    ...MODALS.map(
      modal =>
        new HtmlWebpackPlugin({
          inject: false,
          template: `${MODALS_DIR}/${modal}`,
          filename: `./modals/${modal.replace(/\.pug/, '.html')}`
        }),
    ),
    beautifyOptions,
  ]
};
