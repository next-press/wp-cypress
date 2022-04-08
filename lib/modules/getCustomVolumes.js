const path = require('path');
const glob = require('glob');

/**
 * Retrieve the required custom customVolumes data from config.
 *
 * @param {array} globs
 * @param {string} type
 * @returns {array}
 */
const getcustomVolumes = (volumeGlobs = []) => {
  const customVolumes = [];

  volumeGlobs.forEach((x) => {
    const location = path.resolve(x.localPath);

    glob.sync(location).forEach((file) => {
      const name = path.basename(file);
      const item = {
        name,
        path: file,
        volume: `${file}:${x.path}`,
      };

      if (!customVolumes.some((y) => y.path === item.path)) {
        customVolumes.push(item);
      }
    });
  });

  return { customVolumes };
};

module.exports = getcustomVolumes;
