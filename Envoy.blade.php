@servers(['web' => 'deployer@10.0.0.9'])

@setup
    $repository = 'git@gitlab.code8.cz:burt/drmouse-api.git';
    $branch = isset($branch) ? $branch : "master";
    $release = date('YmdHis');

    if ($branch === 'develop') {
        $app_dir = '/var/www/drmouse-develop/server';
    } else {
        $app_dir = '/var/www/drmouse-api';
    }

    $releases_dir = $app_dir . '/releases';
    $new_release_dir = $releases_dir .'/'. $release;

@endsetup

@story('deploy')
    clone_repository
    run_composer
    update_symlinks
    migrate
@endstory

@task('clone_repository')
    echo "Deploy branch ({{ $branch }})"
    echo 'Cloning repository'
    [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
    git clone --branch {{ $branch }} --depth 1 {{ $repository }} {{ $new_release_dir }}
    cd {{ $new_release_dir }}
    git reset --hard {{ $commit }}
@endtask

@task('run_composer')
    echo "Starting deployment ({{ $release }})"
    cd {{ $new_release_dir }}
    composer install --prefer-dist --no-scripts -q -o
@endtask

@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage
    ln -s {{ $new_release_dir }}/storage/app/public {{ $new_release_dir }}/public/storage

    echo 'Linking .env file'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env

    echo 'Setup write permissions'
    chmod -R 777 {{ $new_release_dir }}/bootstrap/cache/

    echo 'Linking current release'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
@endtask

@task('migrate')
    echo "Migrate"
    cd {{ $new_release_dir }}
    php artisan migrate
@endtask
