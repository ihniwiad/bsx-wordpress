# HowTo

This Bootstrap 4 based WordPress Theme can be used together with the custom Gutenberg Blocks Plugin [BSX Blocks](https://github.com/ihniwiad/bsx-blocks).


## Create `.env`

Example workspace setting (using publishing from workspace to WordPress):

```
...
  ┗ workspace
    ┗ my-project
      ┣ bsx-wordpress
      ┗ bsx-blocks
...
  ┗ htdocs
    ┗ my-projects-wordpress
      ┗ wp-content
        ┣ themes
        ┃ ┗ bsx-wordpress
        ┗ plugins
          ┗ bsx-blocks
```

You need the following variables if you use a workspace outside your WordPress folder (as seen above):

* `FOLDER_NAME` ... Folder name for publishing into your WordPress Theme folder
* `PUBLISH_PATH` ... Path to publish into your WordPress Theme folder

Example:

```
FOLDER_NAME=bsx-wordpress
PUBLISH_PATH=../../../../../Applications/MAMP/htdocs/wordpress-testing/wp-content/themes/
```

All of your Plugin’s files but `node_modules` will be copied to this folder (as `bsx-blocks` folder) each time you build.

**NOTE:** Please take care since publishing will **delete** (and copy again) a folder **outside your Workspace** each time you build or change.


## Install & build

* Run `npm install`
* Run `npm run build`