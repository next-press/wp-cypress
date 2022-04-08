const fs   = require('fs');
const path = require('path');

/**
 * Retrieve the required plug or theme data from dir in config.
 *
 * @param {array} globs
 * @param {string} type
 * @returns {array}
 */
const getThemeAndPluginDataFromDir = (pluginDir, themeDir) => {

  let pluginsFromDir = [];
  let themesFromDir = [];

  
  if(pluginDir) {

    const pluginsLocation = path.resolve(pluginDir);

    pluginsFromDir = fs.readdirSync(pluginsLocation, { withFileTypes: true })
      .filter((item) => item.isDirectory())
      .map(({name}) => ({
        name,
        path: `${themesLocation}/${name}`,
        volume: `${themesLocation}/${name}:/var/www/html/wp-content/plugins/${name}`,
      }));

  }

  if(themeDir) {

    const themesLocation = path.resolve(themeDir);

    themesFromDir = fs.readdirSync(themesLocation, { withFileTypes: true })
      .filter((item) => item.isDirectory())
      .map(({name}) => ({
        name,
        path: `${themesLocation}/${name}`,
        volume: `${themesLocation}/${name}:/var/www/html/wp-content/themes/${name}`,
      }));

  }

  return { pluginsFromDir, themesFromDir };

};

module.exports = getThemeAndPluginDataFromDir;
