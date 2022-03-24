import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    changeRoute (event) {
        const route = event.params.route
        window.location.href = route
    }
}
