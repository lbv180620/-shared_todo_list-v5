const { watch } = require('gulp');
const browserSync = require('browser-sync').create();

const startServer = () => {
    browserSync.init({
        proxy: '192.168.33.22:8080',
    });

    watch('./public').on('change', browserSync.reload);
}

exports.s = startServer;
