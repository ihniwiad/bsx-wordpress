# HowTo

This Bootstrap 4 based WordPress Theme can be used together with the custom Gutenberg Blocks Plugin [BSX Blocks](https://github.com/ihniwiad/bsx-blocks).


## Development

* Use Node 20 (with NVM `nvm use 20`)
* Install Node dependencies: `npm install`
* Develop: `npm run watch`
* Build: `npm run build`
* (Optional, if using separate workspace) publish: `npm run publish`


### Optionally separate workspace from WordPress folder

You can install you repository in a separate workspace and publish your (dev-) build to your target WordPress themes folder (will be done automatically after each (dev-) build). In addition, you can use the publish task to publish without build.

E.g.:

```
<YOUR_USERS_FOLDER>
  ┣ workspace
  ┃ ┣ saneware-wp
  ┃ ┗ wp-multi-block-plugin
  ┃
  ┗ Herd
    ┗ my-project-wordpress-folder
      ┗ wp-content
        ┣ themes
        ┃ ┗ saneware-wp
        ┗ plugins
          ┗ wp-multi-block-plugin
```

* Create `.env`, add config data with
    * `PUBLISH_PATH` ... path to your wordpress themes folder
    * `FOLDER_NAME` ... name of your them folder to be created

E.g.:

```
PUBLISH_PATH="/Users/<YOUR_USERS_FOLDER>/Herd/saneware/wp-content/themes/"
FOLDER_NAME=saneware
```
