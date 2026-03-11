import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { TextPlugin } from 'gsap/TextPlugin'
import { Draggable } from 'gsap/Draggable'

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger, TextPlugin, Draggable)

// Buat instance GSAP global
export default gsap

// Export plugins jika diperlukan
export { ScrollTrigger, TextPlugin, Draggable }

// Helper functions untuk animasi umum
export const fadeIn = (element, duration = 1, delay = 0) => {
    return gsap.fromTo(
        element,
        { opacity: 0, y: 20 },
        { opacity: 1, y: 0, duration, delay }
    )
}

export const fadeInUp = (element, options = {}) => {
    return gsap.fromTo(
        element,
        { opacity: 0, y: 50 },
        {
            opacity: 1,
            y: 0,
            duration: options.duration || 1,
            delay: options.delay || 0,
            stagger: options.stagger || 0
        }
    )
}

export const scaleIn = (element, duration = 0.5) => {
    return gsap.from(element, {
        scale: 0,
        duration,
        ease: "back.out(1.7)"
    })
}
