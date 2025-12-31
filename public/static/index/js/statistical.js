/**
 * 统计请求
 */
var ys = function() {
  this.v = 'v1.0.0'
}

ys.fn = ys.prototype
var host = window.location.origin + '/';

/**
 * [isEmpty description]
 *
 * @param  {[type]}  string [description]
 * @return {Boolean}        [description]
 */
ys.isEmpty = function(string) {
  if (string === ''
    || string === undefined
    || string === null) {
    return true
  }
  return false
}

/**
 * 判断是否为手机
 *
 * @return {Boolean} [description]
 */
ys.isMobile = function() {
  var userAgentInfo = navigator.userAgent
  var mobileAgents = ["Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"]
  var mobile_flag = false

  for (var v = 0; v < mobileAgents.length; v++) {
    if (userAgentInfo.indexOf(mobileAgents[v]) > 0) {
      mobile_flag = true
      break
    }
  }

  var screen_width = window.screen.width
  var screen_height = window.screen.height

  //根据屏幕分辨率判断是否是手机
  if(screen_width < 500 && screen_height < 800) {
    mobile_flag = true
  }

  return mobile_flag
}

/**
 * 异步请求
 *
 * @return {[type]} [description]
 */
ys.request = function(url, data, callback, method) {
  var xhr = new XMLHttpRequest()
  method = this.isEmpty(method) ? 'GET' : method

  xhr.open(method, url, true)
  xhr.responseType = 'json'

  xhr.onreadystatechange = function() {
    if (xhr.readyState === xhr.DONE) {
      if (xhr.status === 200) {
        //callback(xhr.response)
      } else {
        //callback(xhr.response)
      }
    }
  }

  xhr.ontimeout = function(event) {
    console.log('请求超时')
  }
  xhr.setRequestHeader('Content-Type','application/json')
  xhr.send()
}

/**
 * 启动统计
 *
 * @return {[type]} [description]
 */
ys.start = function(host, url) {
  let isMobile = this.isMobile() ? 1 : 0
  let params = '?url=' + url + '&is_mobile=' + isMobile

  ys.request(host + params, '', function(res) {})
}
