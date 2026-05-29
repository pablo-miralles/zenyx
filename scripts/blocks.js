/**
 * Build or watch all blocks in blocks/* that have block.json and src/index.js.
 * Usage: node scripts/blocks.js build | watch
 */
const fs = require('fs');
const path = require('path');
const {
    spawnSync,
    spawn
} = require('child_process');
const root = path.resolve(__dirname, '..');
const blocksDir = path.join(root, 'blocks');
const isWindows = process.platform === 'win32';
const wpScriptsBin = path.join(root, 'node_modules', '.bin', 'wp-scripts' + (isWindows ? '.cmd' : ''));

function getBlockSlugs() {
    if (!fs.existsSync(blocksDir)) return [];
    const entries = fs.readdirSync(blocksDir, {
        withFileTypes: true
    });
    const slugs = [];
    for (const ent of entries) {
        if (!ent.isDirectory()) continue;
        const blockPath = path.join(blocksDir, ent.name);
        const hasBlockJson = fs.existsSync(path.join(blockPath, 'block.json'));
        const hasSrc = fs.existsSync(path.join(blockPath, 'src', 'index.js'));
        if (hasBlockJson && hasSrc) slugs.push(ent.name);
    }
    return slugs;
}

function buildBlock(slug) {
    const srcDir = `blocks/${slug}/src`;
    const outDir = `blocks/${slug}/build`;
    const viewJs = path.join(blocksDir, slug, 'src', 'view.js');
    const args = ['build', `--webpack-src-dir=${srcDir}`, `--output-path=${outDir}`];
    if (fs.existsSync(viewJs)) {
        args.push('index.js', 'view.js');
    }
    const result = spawnSync(wpScriptsBin, args, {
        stdio: 'inherit',
        cwd: root
    });
    return result.status === 0;
}

function watchBlock(slug) {
    const srcDir = `blocks/${slug}/src`;
    const outDir = `blocks/${slug}/build`;
    const viewJs = path.join(blocksDir, slug, 'src', 'view.js');
    const args = ['start', `--webpack-src-dir=${srcDir}`, `--output-path=${outDir}`];
    if (fs.existsSync(viewJs)) {
        args.push('index.js', 'view.js');
    }
    const child = spawn(wpScriptsBin, args, {
        stdio: 'inherit',
        cwd: root
    });
    return child;
}
const cmd = process.argv[2] || 'build';
if (cmd === 'build') {
    const slugs = getBlockSlugs();
    if (slugs.length === 0) {
        console.log('No blocks with block.json + src/index.js found in blocks/');
        process.exit(0);
    }
    console.log('Building blocks:', slugs.join(', '));
    let failed = false;
    for (const slug of slugs) {
        console.log('\n---', slug, '---');
        if (!buildBlock(slug)) failed = true;
    }
    process.exit(failed ? 1 : 0);
} else if (cmd === 'watch') {
    const slugs = getBlockSlugs();
    if (slugs.length === 0) {
        console.log('No blocks with block.json + src/index.js found in blocks/');
        process.exit(0);
    }
    console.log('Watching blocks:', slugs.join(', '));
    const children = slugs.map(watchBlock);
    const killAll = () => {
        children.forEach(c => c.kill('SIGINT'));
        process.exit(0);
    };
    process.on('SIGINT', killAll);
    process.on('SIGTERM', killAll);
} else {
    console.error('Usage: node scripts/blocks.js build|watch');
    process.exit(1);
}