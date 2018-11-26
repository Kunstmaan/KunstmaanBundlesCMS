import browserSync from 'browser-sync';

export default function createServerTask({
    serverName = 'server',
    config = undefined,
}) {
    return function server(done) {
        browserSync
            .create(serverName)
            .init(config, done);
    };
}
