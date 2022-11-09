function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function ($) {
  var Chat = /*#__PURE__*/function () {
    function Chat(name, level) {
      _classCallCheck(this, Chat);

      this.el = $('body');
      this.name = name;
      this.level = level;
      this.chatBtn = this.el.find(".rtcl-contact-seller");
      this.listing_id = 0;
      this.isMobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4));
      this.conversationWrap = $('<div class="rtcl-chat-container"><div class="rtcl-message-container"><ul class="rtcl-messages-list"></ul></div></div>');
      this.chatForm = $('<div class="rtcl-chat-form-wrap"><form class="rtcl-chat-form" autocomplete="off"><div class="rtcl-chat-input-wrap"><input type="text" class="rtcl-chat-input" name="message" placeholder="' + rtcl_chat.lang.message_placeholder + '"></div><button class="rtcl-chat-send" type="submit"><i class="rtcl-icon rtcl-icon-paper-plane"></i></button></form></div>');
      this.current_user_id = rtcl_chat.current_user_id;
      this.init();
    }

    _createClass(Chat, [{
      key: "init",
      value: function init() {
        this.addEventListeners();
      }
    }, {
      key: "startChatHandler",
      value: function startChatHandler(e) {
        e.preventDefault();

        var _self = $(e.target);

        this.listing_id = _self.data('listing_id') || 0;
        this.generateChatModal();
      }
    }, {
      key: "generateChatModal",
      value: function generateChatModal() {
        var that = this;

        if (!$(document).find('#rtcl-chat-modal-wrap').length) {
          var startMessagesInterval = function startMessagesInterval() {
            clearInterval(data.messageInterval);
            data.messageInterval = setInterval(function () {
              fetchMessages();
            }, 5000);
          };

          var fetchMessages = function fetchMessages() {
            if (data.con_id) {
              $.ajax({
                url: rtcl_chat.ajaxurl,
                method: 'POST',
                data: {
                  action: 'rtcl_chat_ajax_get_messages',
                  con_id: data.con_id
                },
                dataType: 'JSON',
                success: function success(res) {
                  if (res.success) {
                    data.con_messages = res.messages;
                    populateMessages();
                    startMessagesInterval();
                  }
                }
              });
            }
          };

          var populateMessages = function populateMessages() {
            if (data.con_messages.length) {
              var list = '';
              data.con_messages.map(function (item) {
                var own_id = rtcl_chat.current_user_id || 0;
                var is_own_message = own_id && own_id === item.source_id;
                var created_at = moment(item.created_at).format(rtcl_chat.date_time_format);
                var message_class = is_own_message ? ' own-message' : '';
                message_class = "rtcl-message-wrap".concat(message_class);
                var read_status = '';

                if (is_own_message && item.is_read !== undefined) {
                  var icon_class = parseInt(item.is_read, 10) === 1 ? ' rtcl-read' : '';
                  icon_class = "rtcl-icon rtcl-icon-ok".concat(icon_class);
                  read_status = '<span class="read-receipt-status"><i class="' + icon_class + '"> </i></span>';
                }

                function formatMessage(message) {
                  if (!message) {
                    return '';
                  }

                  return message.replace(/\\'/gi, "'").replace(/\\"/gi, '"');
                }

                function markAsRead() {
                  if (item.is_read === "0" && item.source_id !== rtcl_chat.current_user_id) {
                    $.post(rtcl_chat.ajaxurl, {
                      action: 'rtcl_chat_ajax_message_mark_as_read',
                      message_id: item.message_id
                    });
                  }
                }

                markAsRead();
                list += '<li class="' + message_class + '"><div class="rtcl-message"><div class="rtcl-message-text">' + formatMessage(item.message) + '</div><div class="rtcl-message-meta"><span class="message-time">' + created_at + '</span>' + read_status + '</div></div></li>';
              });
              modal.find('.rtcl-messages-list').html(list);

              var _target = modal.find('.rtcl-messages-list');

              _target.scrollTop(_target.prop("scrollHeight"));
            }
          };

          var modal = $('<div id="rtcl-chat-modal-wrap" class="rtcl-close">' + '<div id="rtcl-chat-modal">' + '<div class="rtcl-chat-modal-handle">' + '<div class="handle-title">' + rtcl_chat.lang.chat_txt + '</div><i class="rtcl-icon rtcl-icon-down-open"></i>' + '</div>' + '<div class="rtcl-chat-model-body"><div class="rtcl-loading">' + rtcl_chat.lang.loading + '</div></div>' + '</div>' + '</div>');
          modal.find('.rtcl-chat-modal-handle').on("click", function () {
            $(this).closest("#rtcl-chat-modal-wrap").toggleClass('rtcl-close');
          });
          var data = {
            con_id: 0,
            con_messages: [],
            messageInterval: null
          };
          $.ajax({
            url: rtcl_chat.ajaxurl,
            data: {
              listing_id: this.listing_id,
              action: 'rtcl_chat_ajax_start_conversation'
            },
            type: "POST",
            dataType: 'JSON',
            beforeSend: function beforeSend() {
              modal.find('.rtcl-chat-modal-handle').trigger('click');
            },
            success: function success(res) {
              if (res.success) {
                if (res.con_id) {
                  data.con_id = res.con_id;
                  data.con_messages = res.con_messages;
                }

                var conversationWrap = that.conversationWrap;
                conversationWrap.find('.rtcl-message-container').append(that.chatForm);
                modal.find('.rtcl-chat-model-body').html(conversationWrap);
                populateMessages();
                startMessagesInterval();
                modal.find('.rtcl-chat-input').on('keyup', function () {
                  var _input = $(this),
                      val = _input.val(),
                      _button = _input.closest('form').find('button');

                  if (val) {
                    _button.addClass('rtcl-active');
                  } else {
                    _button.removeClass('rtcl-active');
                  }
                });
                modal.find('.rtcl-chat-form').on('change', '.rtcl-chat-input', function (e) {
                  e.preventDefault();

                  var _input = $(this),
                      _input_value = _input.val(),
                      tempElement = document.createElement('div');

                  tempElement.innerHTML = _input_value;
                  var inputMessage = tempElement.textContent || tempElement.innerText || "";
                  inputMessage = inputMessage.substr(0, 300);

                  _input.val(inputMessage);

                  return false;
                });
                modal.find('.rtcl-chat-form').on('submit', function (e) {
                  e.preventDefault();

                  var _form = $(this),
                      _input = _form.find('.rtcl-chat-input'),
                      _button = _form.find('button'),
                      message = _input.val(),
                      form_data = {
                    listing_id: that.listing_id,
                    message: message,
                    action: 'rtcl_chat_ajax_visitor_send_message'
                  };

                  if (data.con_id) {
                    form_data.con_id = data.con_id;
                  }

                  if (message) {
                    $.ajax({
                      url: rtcl_chat.ajaxurl,
                      data: form_data,
                      type: "POST",
                      dataType: 'JSON',
                      beforeSend: function beforeSend() {
                        data.con_messages.push({
                          message_id: new Date().getTime(),
                          message: message,
                          source_id: rtcl_chat.current_user_id,
                          created_at: moment().format('YYYY-MM-DD HH:mm:ss')
                        });
                        populateMessages();

                        _input.val('');

                        _button.removeClass('rtcl-active');
                      },
                      success: function success(res) {
                        if (res.con_id) {
                          data.con_id = res.con_id;
                          fetchMessages();
                          startMessagesInterval();
                        }
                      },
                      error: function error() {}
                    });
                  }

                  return false;
                });
              } else {
                modal.find('.rtcl-chat-model-body').html(rtcl_chat.lang.no_permission);
              }
            },
            error: function error() {
              modal.find('.rtcl-chat-model-body').html(rtcl_chat.lang.server_error);
            }
          });
          this.el.prepend(modal);
        }
      }
    }, {
      key: "addEventListeners",
      value: function addEventListeners() {
        this.chatBtn.length && this.chatBtn.on("click", this.startChatHandler.bind(this));
      }
    }]);

    return Chat;
  }();

  new Chat();
})(jQuery);
