/* eslint-disable */

import xhr from '../xhr';

let event;

beforeAll(() => {
    event = {
        href: '/app_dev.php/nl/legal/toggle-all-cookies',
        data: '',
    };
});

afterAll(() => {
    event = '';
});

describe('Xhr', () => {
    describe('get', () => {
        describe('takes a url', () => {
            it('should return a Promise', () => {
                expect(xhr.get(event.href)).toBeInstanceOf(Promise);
            });
        });

        describe('takes a url', () => {
            it('should return a Promise', () => {
                test((err) => {
                    expect(() => { err; }).toThrow();
                    done();
                });
            });
        });

        describe('takes a url', () => {
            it('should return an Error', () => {
                expect(() => { xhr.get(''); }).toThrow();
            });
        });
    });

    describe('post', () => {
        describe('takes an url and data', () => {
            it('should return a Promise', () => {
                expect(xhr.post(event.href, event.data)).toBeInstanceOf(Promise);
            });
        });
    });
});
