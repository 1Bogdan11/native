export const isIos = () => {
    return [
      'iPad Simulator',
      'iPhone Simulator',
      'iPod Simulator',
      'iPad',
      'iPhone',
      'iPod'
    ].includes(navigator.platform)
    || /^((?!chrome|android).)*safari/i.test(navigator.userAgent)
    || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
}