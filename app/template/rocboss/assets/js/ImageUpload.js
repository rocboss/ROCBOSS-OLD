var EXIF = function() {
        function x(a, e) {
            e || a.match(/^data\:([^\;]+)\;base64,/mi);
            a = a.replace(/^data\:([^\;]+)\;base64,/gmi, "");
            for (var b = atob(a), f = b.length, d = new ArrayBuffer(f), c = new Uint8Array(d), k = 0; k < f; k++) c[k] = b.charCodeAt(k);
            return d
        }
        function y(a, e) {
            var b = new XMLHttpRequest;
            b.open("GET", a, !0);
            b.responseType = "blob";
            b.onload = function(a) {
                200 != this.status && 0 !== this.status || e(this.response)
            };
            b.send()
        }
        function z(a, e) {
            function b(b) {
                var c;
                a: if (c = new DataView(b), 255 != c.getUint8(0) || 216 != c.getUint8(1)) c = !1;
                else {
                    for (var d = 2, f = b.byteLength, l; d < f;) {
                        if (255 != c.getUint8(d)) {
                            c = !1;
                            break a
                        }
                        l = c.getUint8(d + 1);
                        if (225 == l) {
                            c = A(c, d + 4, c.getUint16(d + 2) - 2);
                            break a
                        } else d += 2 + c.getUint16(d + 2)
                    }
                    c = void 0
                }
                a: if (d = new DataView(b), 255 != d.getUint8(0) || 216 != d.getUint8(1)) b = !1;
                else {
                    f = 2;
                    for (l = b.byteLength; f < l;) {
                        var g = d,
                            h = f;
                        if (56 === g.getUint8(h) && 66 === g.getUint8(h + 1) && 73 === g.getUint8(h + 2) && 77 === g.getUint8(h + 3) && 4 === g.getUint8(h + 4) && 4 === g.getUint8(h + 5)) {
                            g = d.getUint8(f + 7);
                            0 !== g % 2 && (g += 1);
                            0 === g && (g = 4);
                            l = f + 8 + g;
                            f = d.getUint16(f + 6 + g);
                            d = l;
                            b = new DataView(b);
                            l = {};
                            for (var p = void 0, h = p = h = void 0, g = d; g < d + f;) 28 === b.getUint8(g) && 2 === b.getUint8(g + 1) && (h = b.getUint8(g + 2), h in t && (p = b.getInt16(g + 3), h = t[h], p = q(b, g + 5, p), l.hasOwnProperty(h) ? l[h] instanceof Array ? l[h].push(p) : l[h] = [l[h], p] : l[h] = p)), g++;
                            b = l;
                            break a
                        }
                        f++
                    }
                    b = void 0
                }
                a.exifdata = c || {};
                a.iptcdata = b || {};
                e && e.call(a)
            }
            if (a instanceof Image || a instanceof HTMLImageElement) if (/^data\:/i.test(a.src)) {
                var f = x(a.src);
                b(f)
            } else if (/^blob\:/i.test(a.src)) {
                var d = new FileReader;
                d.onload = function(a) {
                    b(a.target.result)
                };
                y(a.src, function(a) {
                    d.readAsArrayBuffer(a)
                })
            } else {
                var c = new XMLHttpRequest;
                c.onload = function() {
                    if ("200" == c.status) b(c.response);
                    else throw "Could not load image";
                    c = null
                };
                c.open("GET", a.src, !0);
                c.responseType = "arraybuffer";
                c.send(null)
            } else window.FileReader && (a instanceof window.Blob || a instanceof window.File) && (d = new FileReader, d.onload = function(a) {
                debug && console.log("Got file of length " + a.target.result.byteLength);
                b(a.target.result)
            }, d.readAsArrayBuffer(a))
        }
        function u(a, e, b, f, d) {
            var c = a.getUint16(b, !d),
                k = {},
                m, q, r;
            for (r = 0; r < c; r++) m = b + 12 * r + 2, q = f[a.getUint16(m, !d)], k[q] = B(a, m, e, b, d);
            return k
        }
        function B(a, e, b, f, d) {
            var c = a.getUint16(e + 2, !d);
            f = a.getUint32(e + 4, !d);
            b = a.getUint32(e + 8, !d) + b;
            var k, m;
            switch (c) {
            case 1:
            case 7:
                if (1 == f) return a.getUint8(e + 8, !d);
                b = 4 < f ? b : e + 8;
                e = [];
                for (c = 0; c < f; c++) e[c] = a.getUint8(b + c);
                return e;
            case 2:
                return q(a, 4 < f ? b : e + 8, f - 1);
            case 3:
                if (1 == f) return a.getUint16(e + 8, !d);
                b = 2 < f ? b : e + 8;
                e = [];
                for (c = 0; c < f; c++) e[c] = a.getUint16(b + 2 * c, !d);
                return e;
            case 4:
                if (1 == f) return a.getUint32(e + 8, !d);
                e = [];
                for (c = 0; c < f; c++) e[c] = a.getUint32(b + 4 * c, !d);
                return e;
            case 5:
                if (1 == f) return k = a.getUint32(b, !d), m = a.getUint32(b + 4, !d), a = new Number(k / m), a.numerator = k, a.denominator = m, a;
                e = [];
                for (c = 0; c < f; c++) k = a.getUint32(b + 8 * c, !d), m = a.getUint32(b + 4 + 8 * c, !d), e[c] = new Number(k / m), e[c].numerator = k, e[c].denominator = m;
                return e;
            case 9:
                if (1 == f) return a.getInt32(e + 8, !d);
                e = [];
                for (c = 0; c < f; c++) e[c] = a.getInt32(b + 4 * c, !d);
                return e;
            case 10:
                if (1 == f) return a.getInt32(b, !d) / a.getInt32(b + 4, !d);
                e = [];
                for (c = 0; c < f; c++) e[c] = a.getInt32(b + 8 * c, !d) / a.getInt32(b + 4 + 8 * c, !d);
                return e
            }
        }
        function q(a, e, b) {
            var f = "";
            for (n = e; n < e + b; n++) f += String.fromCharCode(a.getUint8(n));
            return f
        }
        function A(a, e) {
            if ("Exif" != q(a, e, 4)) return !1;
            var b, f, d, c = e + 6;
            if (18761 == a.getUint16(c)) b = !1;
            else if (19789 == a.getUint16(c)) b = !0;
            else return !1;
            if (42 != a.getUint16(c + 2, !b)) return !1;
            f = a.getUint32(c + 4, !b);
            if (8 > f) return !1;
            f = u(a, c, c + f, v, b);
            if (f.ExifIFDPointer) for (d in b = u(a, c, c + f.ExifIFDPointer, w, b), b) {
                switch (d) {
                case "ExifVersion":
                case "FlashpixVersion":
                    b[d] = String.fromCharCode(b[d][0], b[d][1], b[d][2], b[d][3])
                }
                f[d] = b[d]
            }
            return f
        }
        var w = {
            36864: "ExifVersion",
            40960: "FlashpixVersion",
            40961: "ColorSpace",
            40962: "PixelXDimension",
            40963: "PixelYDimension",
            37122: "CompressedBitsPerPixel",
            41492: "SubjectLocation"
        },
            v = {
                256: "ImageWidth",
                257: "ImageHeight",
                34665: "ExifIFDPointer",
                258: "BitsPerSample",
                259: "Compression",
                262: "PhotometricInterpretation",
                274: "Orientation",
                277: "SamplesPerPixel",
                284: "PlanarConfiguration",
                530: "YCbCrSubSampling",
                531: "YCbCrPositioning",
                282: "XResolution",
                283: "YResolution",
                296: "ResolutionUnit",
                273: "StripOffsets",
                278: "RowsPerStrip",
                279: "StripByteCounts",
                513: "JPEGInterchangeFormat",
                514: "JPEGInterchangeFormatLength",
                301: "TransferFunction",
                318: "WhitePoint",
                319: "PrimaryChromaticities",
                529: "YCbCrCoefficients",
                532: "ReferenceBlackWhite",
                306: "DateTime",
                270: "ImageDescription",
                271: "Make",
                272: "Model",
                305: "Software",
                315: "Artist",
                33432: "Copyright"
            },
            t = {
                120: "caption",
                110: "credit",
                25: "keywords",
                55: "dateCreated",
                80: "byline",
                85: "bylineTitle",
                122: "captionWriter",
                105: "headline",
                116: "copyright",
                15: "category"
            };
        return {
            getTag: function(a, e) {
                if (a.exifdata) return a.exifdata[e]
            },
            getAllTags: function(a) {
                if (!a.exifdata) return {};
                var e;
                a = a.exifdata;
                var b = {};
                for (e in a) a.hasOwnProperty(e) && (b[e] = a[e]);
                return b
            },
            getData: function(a, e) {
                if ((a instanceof Image || a instanceof HTMLImageElement) && !a.complete) return !1;
                a.exifdata ? e && e.call(a) : z(a, e);
                return !0
            },
            Tags: w,
            TiffTags: v
        }
    }();
(function($) {
    $.fn.ImageUpload = function(option) {
        $(this).on('change', function() {
            var self = this,
                img = new Image(),
                reader = new FileReader(),
                canvas = document.createElement('canvas');
            if (canvas.getContext) {
                var ctx = canvas.getContext('2d')
            } else {
                alert("您的浏览器不支持HTML5!");
                self.value = ""
            }
            if (this.files[0].type == 'image/gif') {
                var IsGIF = true
            } else {
                var IsGIF = false
            }
            option.before();
            reader.onload = function(evt) {
                var srcString = evt.target.result;
                img.src = srcString.substring(5, 10) != "image" ? srcString.replace(/(.{5})/, "$1image/jpeg;") : srcString;
                if (IsGIF) {
                    option.after(this.result);
                    dataTrans = this.result
                } else {
                    option.after(img.src)
                }
                img.onload = function() {
                    if (!IsGIF) {
                        var w = img.width,
                            h = img.height,
                            afw = option.setWidth || 500,
                            afh = afw * h / w;
                        var orientation = 1;
                        EXIF.getData(img, function() {
                            orientation = parseInt(EXIF.getTag(img, "Orientation"));
                            orientation = orientation ? orientation : 1
                        });
                        if (w < 100 || h < 100) return false;
                        if (orientation <= 4) {
                            $(canvas).attr({
                                width: afw,
                                height: afh
                            });
                            if (orientation == 3 || orientation == 4) {
                                ctx.translate(afw, afh);
                                ctx.rotate(180 * Math.PI / 180)
                            }
                        } else {
                            $(canvas).attr({
                                width: afh,
                                height: afw
                            });
                            if (orientation == 5 || orientation == 6) {
                                ctx.translate(afh, 0);
                                ctx.rotate(90 * Math.PI / 180)
                            } else if (orientation == 7 || orientation == 8) {
                                ctx.translate(0, afw);
                                ctx.rotate(270 * Math.PI / 180)
                            }
                        }
                        if (navigator.userAgent.match(/iphone/i)) {
                            drawImageIOSFix(ctx, img, 0, 0, w, h, 0, 0, afw, afh)
                        } else {
                            ctx.drawImage(img, 0, 0, afw, afh)
                        }
                        self.value = "";
                        dataTrans = canvas.toDataURL('image/jpeg')
                    }
                    $.ajax({
                        xhr: function() {
                            var xhrobj = $.ajaxSettings.xhr();
                            if (xhrobj.upload) {
                                xhrobj.upload.addEventListener('progress', function(event) {
                                    var percent = 0;
                                    var position = event.loaded || event.position;
                                    var total = event.total || e.totalSize;
                                    if (event.lengthComputable) {
                                        percent = Math.ceil(position / total * 100)
                                    }
                                    option.progress(percent)
                                }, false)
                            }
                            return xhrobj
                        },
                        url: option.url,
                        data: {
                            'base64': dataTrans
                        },
                        type: 'POST',
                        dataType: 'json',
                        success: function(result) {
                            option.success(result)
                        }
                    })
                }
            }
            reader.readAsDataURL(this.files[0]);
            if (this.outerHTML) {
                this.outerHTML = this.outerHTML;
            } else {
                this.value = '';
            }
        });

        function detectVerticalSquash(img) {
            var iw = img.naturalWidth,
                ih = img.naturalHeight;
            var canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = ih;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            var data = ctx.getImageData(0, 0, 1, ih).data;
            var sy = 0;
            var ey = ih;
            var py = ih;
            while (py > sy) {
                var alpha = data[(py - 1) * 4 + 3];
                if (alpha === 0) {
                    ey = py
                } else {
                    sy = py
                }
                py = (ey + sy) >> 1
            }
            var ratio = (py / ih);
            return (ratio === 0) ? 1 : ratio
        }
        function drawImageIOSFix(ctx, img, sx, sy, sw, sh, dx, dy, dw, dh) {
            var vertSquashRatio = detectVerticalSquash(img);
            ctx.drawImage(img, sx * vertSquashRatio, sy * vertSquashRatio, sw * vertSquashRatio, sh * vertSquashRatio, dx, dy, dw, dh)
        }
    }
})(jQuery);