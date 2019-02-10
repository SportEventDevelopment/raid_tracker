var SED = {};

SED.log = function() {
    window.console.log.apply(window.console, [
        '%c[RAID TRACKER]%c ' + Array.prototype.slice.call(arguments).join(' ') +
            ' %c(@' + performance.now().toFixed(2) + 'ms)',
        'background: #BBDEFB; color: #FF5722; font-weight: 600', 'font-weight: 300',
        'color: #AAA'
    ]);
}

export default SED;