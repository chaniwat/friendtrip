# Laravel 5 + Angular 2 Combo

Laravel 5.3 pre-setup with angular 2.1 (and bootstrap 4.0.0-alpha.5)

#### Dependencies
- Laravel 5
- JWT-Auth
- angular2 (with angular-cli)
- angular2-cool-storage
- bootstrap 4
- font-awesome
- material-colors
- jquery 3
- lodash
- moment
- toastr

## Prerequisite

- installed composer - https://getcomposer.org/download/
- Laravel Prerequisite - https://laravel.com/docs/5.3/installation
- installed angular-cli - https://github.com/angular/angular-cli
- installed gulp - https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md

## Instruction

1.) Clone a repository

```
> git clone git@github.com:chaniwat/laravel_ng2-cli.git
```

2.) Delete .git

On UNIX
```
> cd laravel_ng2-cli && rm -rf .git
```

On Windows
```
> cd laravel_ng2-cli && rmdir /s /q ".git"
```

3.) Initial your git repository

```
> git init
> git add .
> git commit -m "Initial commit"
```

## Commands

Start a develop server
```
> npm start

Server start at http://localhost:3000
```

Build for production
```
> npm run build

angular-cli & gulp will do a work for you, just wait :D
```
