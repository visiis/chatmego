export interface LazyLoadOptions {
  rootMargin?: string
  threshold?: number
  placeholder?: string
}

export function useLazyLoad(options: LazyLoadOptions = {}) {
  const {
    rootMargin = '100px',
    threshold = 0.1,
    placeholder = ''
  } = options

  let observer: IntersectionObserver | null = null
  const loadedImages = new Set<string>()

  function init() {
    if (observer) return
    
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target as HTMLImageElement
          const src = img.getAttribute('data-src')
          
          if (src && !loadedImages.has(src)) {
            img.src = src
            loadedImages.add(src)
            observer?.unobserve(img)
          }
        }
      })
    }, {
      rootMargin,
      threshold
    })
  }

  function observe(img: HTMLImageElement) {
    if (!observer) init()
    const src = img.getAttribute('data-src')
    
    if (src && !loadedImages.has(src)) {
      img.src = placeholder
      observer.observe(img)
    }
  }

  function unobserve(img: HTMLImageElement) {
    observer?.unobserve(img)
  }

  function destroy() {
    observer?.disconnect()
    observer = null
    loadedImages.clear()
  }

  return {
    init,
    observe,
    unobserve,
    destroy
  }
}

export function createLazyImageUrl(url: string): string {
  if (!url) return ''
  
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }
  
  if (url.startsWith('/storage/')) {
    return 'https://chatmego.com' + url
  } else if (url.startsWith('storage/')) {
    return 'https://chatmego.com/' + url
  } else if (!url.startsWith('/')) {
    return 'https://chatmego.com/storage/' + url
  } else {
    return 'https://chatmego.com' + url
  }
}
