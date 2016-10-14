# OSMIHelp.org

Source code for the [OSMIHelp.org](http://osmihelp.org) web site.

## Stuff you need

1. npm (js)
2. gulp (js)
3. bower (js)
4. composer (php)
5. fabric (python)

## How to build the site from scratch

1. run `npm install`
2. run `bower install`
3. run `gulp build`
4. run `server.sh`
5. Open <http://localhost:9999> in a browser

## How to change the site

To watch for changes, use the command `gulp watch`. To preview the site locally, use `./server.sh` and open <http://localhost:9999> in a browser.

### HTML

- Edit the files in `templates/`. The `.twig` files are written as static html files into the `public/` folder dynamically by `gulp` as changes are saved.

### CSS

- Edit the `public/less/style.less` file. The `.less` files are written as a single `public/css/style.css` file dynamically by `gulp` as changes are saved.
  
  *The file that's compiled is actually `bootstrap-theme.less`, which includes `style.less` and all the bootstrap framework LESS files* 

## How to deploy the site

1. `git commit` changes to the `master` branch, and `git push` them to GitHub.
2. Use the command `fab update` command to deploy the site

    *ask @funkatron to set up your ssh key for deployment*