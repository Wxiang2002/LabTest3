const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  transpileDependencies: true,
  devServer: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000', // Backend server
        changeOrigin: true,
        pathRewrite: { '^/api': '' },
      },
    },
  },
})
