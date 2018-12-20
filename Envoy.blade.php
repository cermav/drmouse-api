@servers(['web' => 'deployer@10.0.0.9'])
@task('test', ['on' => 'web'])
ls -l
@endtask