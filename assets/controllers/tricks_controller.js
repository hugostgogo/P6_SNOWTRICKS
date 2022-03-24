import { Controller } from "@hotwired/stimulus";
import axios from "axios";

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
  static targets = ["wrapper", "button", "text", "icon"];

  initialize() {
    this.$tricks = {
      index: 1,
    };
    console.log(window);
    console.log(this);
  }

  async loadPage() {
    this.#setLoading(true);
    const { data: rawPage } = await axios.get(`/ajax/tricks/${this.$tricks.index++}`);
    const newCards = new DOMParser()
      .parseFromString(rawPage, "text/html")
      .querySelectorAll(".trickCard");

    newCards.forEach((card) => {
      this.wrapperTarget.appendChild(card);
    });

    console.log(newWrapper)

    this.#setLoading(false);
  }

  #setLoading(state) {
    const iconKeyframes = [
      { transform: this.iconTarget.style.transform },
      { transform: "rotate(-1turn)" },
    ];

    if (state) {
      this.iconTarget.animate(iconKeyframes, {
        duration: 1000,
      });
    } else {
      this.iconTarget.getAnimations().forEach((animation) => {
        animation.commitStyles();
        animation.cancel();
      });
    }
  }
}
