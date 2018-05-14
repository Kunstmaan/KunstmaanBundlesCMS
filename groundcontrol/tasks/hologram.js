import { spawn } from 'child_process';

export default function createHologramTask({cwd = undefined}) {
    return function hologram() {
        return spawn('bundle', ['exec', 'hologram'], {
            cwd: cwd
        });
    };
}
