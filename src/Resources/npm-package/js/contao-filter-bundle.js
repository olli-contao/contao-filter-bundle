import '@hundh/contao-utils-bundle';

class FilterBundle {

  static init() {
    FilterBundle.registerEvents();
  }

  static registerEvents() {
    document.addEventListener('filterAsyncSubmit', function(event) {
      event.preventDefault();
      FilterBundle.asyncSubmit(event.detail.form);
    })

    utilsBundle.event.addDynamicEventListener('change',
        '.mod_filter form[data-async] input[data-submit-on-change], .mod_filter form[data-async] [data-submit-on-change] input',
        function(element, event) {
          event.preventDefault();
          FilterBundle.asyncSubmit(element.form);
        });

    utilsBundle.event.addDynamicEventListener('click', '.mod_filter form[data-async] button[type="submit"]',
        function(element, event) {
          event.preventDefault();
          FilterBundle.asyncSubmit(element.form);
        });
  }

  static asyncSubmit(form) {
    let method = form.getAttribute('method'),
        action = form.getAttribute('action'),
        data = FilterBundle.getData(form),
        config = FilterBundle.getConfig(form);

    if ('get' === method || 'GET' === method) {
      utilsBundle.ajax.get(action, data, config);
    } else {
      utilsBundle.ajax.post(action, data, config);
    }
  }

  static getConfig(form) {
    return {
      onSuccess: FilterBundle.onSuccess,
      beforeSubmit: FilterBundle.beforeSubmit,
      afterSubmit: FilterBundle.afterSubmit,
      form: form,
      headers: FilterBundle.getRequestHeaders(form.getAttribute('method')),
    };
  }

  static getRequestHeaders(method) {
    if ('get' === method || 'GET' === method) {
      return {'X-Requested-With': 'XMLHttpRequest'};
    } else {
      return {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8\n',
      };
    }
  }

  static onSuccess(request) {
    let response = 'undefined' !== request.response ? JSON.parse(request.response) : null;

    if (null === response) {
      return;
    }

    if ('undefined' === response.filterName) {
      console.log('Error', 'Es wurde kein Filtername gesetzt.');
      return;
    }

    if ('undefined' === response.filter) {
      console.log('Error', 'Es wurde kein Filter zurück geliefert.');
      return;
    }

    let form = document.querySelector('form[name="' + response.filterName + '"]');
    FilterBundle.replaceFilterForm(form, response.filter);

    form.setAttribute('data-response', request.response);
    form.setAttribute('data-submit-success', 1);

    form.dispatchEvent(new CustomEvent('filterAjaxComplete', {detail: form, bubbles: true, cancelable: true}));
  }

  static beforeSubmit(url, data, config) {
    let form = config.form;
    form.setAttribute('data-submit-success', 0);
    form.setAttribute('data-response', '');
    form.querySelectorAll('input:not(.disabled), button[type="submit"]').forEach((elem) => {
      elem.disabled = true;
    });

    form.classList.add('submitting');
  }

  static afterSubmit(url, data, config) {
    let form = config.form;
    form.querySelectorAll('[disabled]').forEach((elem) => {
      elem.disabled = false;
    });

    form.classList.remove('submitting');
  }

  static getData(form) {
    let json = {},
        data = form.querySelectorAll('input:checked, input[type="hidden"], input[type="text"]:not([value=""])');

    data.forEach((elem) => {
      if('' !== elem.value) {
        json[elem.name] = elem.value;
      }
    });

    json.filterName = form.getAttribute('name');

    return json;
  }

  static replaceFilterForm(form, filter) {
    form.innerHTML = filter;

    // run embedded js code (example contao captcha field)
    form.querySelectorAll('script').forEach(script => {
      try {
        eval(script.innerHTML || script.innerText);
      } catch (e) {
      }
    });
  }
}

export {FilterBundle};