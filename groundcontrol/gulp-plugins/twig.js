import through from 'through2';
import gutil from 'gulp-util';
import Twig from 'twig';

export default function twig(options) {

    return through.obj(function (file, enc, cb) {
        if (file.isNull()) {
            cb(null, file);
            return;
        }

        if (file.isStream()) {
            cb(new gutil.PluginError('twig', 'Streaming not supported'));
            return;
        }

        try {
            let contents = file.contents.toString();

            // Twig.js uses different escaping compared to the PHP version
            // This line was failing the renderering, as we don't put backlashes in the testdata it could be removed
            contents = contents.replace(/\|replace\(\{\'\\\\\'\:\'\/\'\}\)/gmi, '');
            
            const template = Twig.twig({
                data: contents
            });
            const result = template.render(options);
            file.contents = new Buffer(result);

            cb(null, file);
        } catch (e) {
            cb(e.message);
        }
    });

};