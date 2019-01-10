import del from 'del';

export default function createCleanTask({
    target = undefined,
}) {
    return function clean() {
        return del(target);
    };
}
