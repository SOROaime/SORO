module.exports = {
    apps: [
        {
            name: 'shoplaraval',
            script: 'php8.2',
            args: 'artisan serve --host=0.0.0.0 --port=8000',
            cwd: '/home/user/webapp',
            env: {
                APP_ENV: 'local',
            },
            watch: false,
            instances: 1,
            exec_mode: 'fork'
        }
    ]
}
