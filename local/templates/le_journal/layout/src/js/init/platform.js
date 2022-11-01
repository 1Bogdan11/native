import { isIos } from '~/js/helpers/platform-detect'

isIos() && document.querySelector("body").classList.add("is-ios")