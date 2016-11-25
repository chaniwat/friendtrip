const gulp = require('gulp');
const fs = require('fs');
const copydir = require('copy-dir');
const del = require('del');

/*
  GULP cmd
 */

gulp.task('cpybuild', ['delPublic', 'cpyPublic', 'cpyBootstrap', 'cpyDist', 'delDist']);

gulp.task('delPublic', () => {
  return del.sync(['public_html/**/*']);
});

gulp.task('cpyPublic', () => {
  return copydir.sync('old_public/', 'public_html/');
});

gulp.task('cpyBootstrap', () => {
  return fs.renameSync('out_dist/index.html', 'resources/views/bootstrap.blade.php');
});

gulp.task('cpyDist', () => {
  return copydir.sync('out_dist/', 'public_html/');
});

gulp.task('delDist', () => {
  return del.sync(['out_dist/**/*']);
});