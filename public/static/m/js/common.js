/**
 * aui 基于jq和aui
 *
 * 时间：2020-03-18
 */

"use strict";

var _671_aui = function() {
  this.v = "1.0.0"
  this.t = new auiToast({})
  this.d = new auiDialog({})
};

_671_aui.fn = _671_aui.prototype

/**
 * 链接跳转
 * @param  {[type]} url [description]
 * @return {[type]}     [description]
 */
_671_aui.fn.urlJump = function(url) {
  window.location.href = url
}

/**
 * 元素判空
 * @param  string element 需要判空的元素
 * @return bool   true表示为空
 */
_671_aui.fn.is_empty = function(element) {
  if (typeof element === undefined
    || (typeof element === "object" && element === null)
    || typeof element === "string" && element.length === 0
    || element === null)
    return true

  return false
}

/**
 * post请求
 * @param  {[type]} url  [description]
 * @param  {[type]} data [description]
 * @param  {[type]} callback [description]
 * @return {[type]}      [description]
 */
_671_aui.fn.post = function(url, data, callback) {
  if (this.is_empty(data)) {
    data = {}
  }

  this.t.loading({ title: '加载中' })
  $.ajax({
    type: 'post',
    url : url,
    data: data,
    timeout: 3000,
    success: function(res) {
      _671.t.hide()
      callback(res)
    },
    error: function(err) {
      _671.t.hide()
      console.log("内部服务器错误:", err)
    }
  });
}

/**
 * 确定提示框
 * @param  {[type]}   msg      [description]
 * @param  {Function} callback [description]
 * @return {[type]}            [description]
 */
_671_aui.fn.confirm = function(msg, callback) {
  _671.d.alert({
    title: '提示',
    msg: msg,
    buttons: ['确认']
  }, function(ret) {
    if (callback) {
      callback(ret)
    }
  })
}

/**
 * ajax跳转
 * @param  {[type]} url [description]
 * @return {[type]}     [description]
 */
_671_aui.fn.ajaxJump = function(url) {
  $.get(url, function(res) {
    if (res.code === 0) {
      _671.confirm(res.msg)
    } else {
      _671.urlJump(url)
    }
  })
}

var _671 = new _671_aui();
