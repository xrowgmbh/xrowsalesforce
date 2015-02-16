function providePlugin(pluginName, pluginConstructor) {
  var ga = window[window['GoogleAnalyticsObject'] || 'ga'];
  if (ga) ga('provide', pluginName, pluginConstructor);
}

/**
 * Constructor for the campaignLoader plugin.
 */
var CampaignLoader = function(tracker, config) {
  this.tracker = tracker;
  this.nameParam = config.nameParam || 'name';
  this.contentParam = config.contentParam || 'content';
  this.sourceParam = config.sourceParam || 'source';
  this.mediumParam = config.mediumParam || 'medium';
  this.isDebug = config.debug;
};

/**
 * Loads campaign fields from the URL and updates the tracker.
 */
CampaignLoader.prototype.loadCampaignFields = function() {
  this.debugMessage('Loading custom campaign parameters');
  var today = new Date(),
      expire = new Date();
  expire.setTime(today.getTime() + 3600000*24*1);
  var nameValue = getUrlParam(this.nameParam);
  if (nameValue) {
    this.tracker.set('campaignName', nameValue);
    document.cookie = "UACName=" + escape(nameValue) + "; expires=" + expire.toGMTString() + "; path=/";
    this.debugMessage('Loaded campaign name: ' + nameValue);
  }

  var contentValue = getUrlParam(this.contentParam);
  if (contentValue) {
    this.tracker.set('campaignContent', contentValue);
    document.cookie = "UACContent=" + escape(contentValue) + "; expires=" + expire.toGMTString() + "; path=/";
    this.debugMessage('Loaded campaign content: ' + contentValue);
  }

  var sourceValue = getUrlParam(this.sourceParam);
  if (sourceValue) {
    this.tracker.set('campaignSource', sourceValue);
    document.cookie = "UACSource=" + escape(sourceValue) + "; expires=" + expire.toGMTString() + "; path=/";
    this.debugMessage('Loaded campaign source: ' + sourceValue);
  }

  var mediumValue = getUrlParam(this.mediumParam);
  if (mediumValue) {
    this.tracker.set('campaignMedium', mediumValue);
    document.cookie = "UACMedium=" + escape(mediumValue) + "; expires=" + expire.toGMTString() + "; path=/";
    this.debugMessage('Loaded campaign medium: ' + mediumValue);
  }
};

/**
 * Enables / disables debug output.
 */
CampaignLoader.prototype.setDebug = function(enabled) {
  this.isDebug = enabled;
};

/**
 * Displays a debug message in the console, if debugging is enabled.
 */
CampaignLoader.prototype.debugMessage = function(message) {
  if (!this.isDebug) return;
  if (console) console.debug(message);
};

/**
 * Utility function to extract a URL parameter value.
 */
function getUrlParam(param) {
  var match = document.location.search.match('(?:\\?|&)' + param + '=([^&#]*)');
  return (match && match.length == 2) ? decodeURIComponent(match[1]) : '';
}

// Register the plugin.
providePlugin('campaignLoader', CampaignLoader);