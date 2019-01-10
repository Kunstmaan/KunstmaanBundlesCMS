import gulp from 'gulp';
import changed from 'gulp-changed';
import imagemin from 'gulp-imagemin';

export default function createImagesTask({src = undefined, dest = undefined}) {
    return function images() {
        return gulp.src(src)
        // Only optimize changed images
        .pipe(changed(dest))

        // Imagemin
        .pipe(imagemin([
            imagemin.jpegtran({progressive: true}),
            imagemin.optipng({optimizationLevel: 3}),
            imagemin.svgo({
                plugins: [
                    {removeViewBox: false},
                    {cleanupIDs: false}
                ]
            })
        ]))

        // Set desitination
        .pipe(gulp.dest(dest));
    };
}
