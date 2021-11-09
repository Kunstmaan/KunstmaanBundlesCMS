/* eslint-disable */

import utils from '../utils';

let element;
let child;

beforeAll(() => {
    // Create a DIV element
    element = document.createElement('div');
    // Add id
    element.id = 'test';
    // Add classes
    element.classList.add('test');
    element.classList.add('class');
    // Add element to DOM
    document.body.appendChild(element);

    // Create a P element
    child = document.createElement('p');
    // Add id
    child.id = 'child';
    // Add class
    child.classList.add('child');
    // Add child to element
    element.appendChild(child);
});

afterAll(() => {
    // Remove element from DOM
    document.removeChild(element);
    // Destroy element
    element = '';
});

describe('Utils', () => {
    describe('hasClass', () => {
        describe('takes an HTMLElement and a class name', () => {
            it('should find the class and return true', () => {
                expect(utils.hasClass(element, 'class')).toBe(true);
            });
        });

        describe('takes an id and a class name', () => {
            it('should find the class and return true', () => {
                expect(utils.hasClass('#test', 'class')).toBe(true);
            });
        });

        describe('takes a class name and a class name', () => {
            it('should find the class and return true', () => {
                expect(utils.hasClass('.test', 'class')).toBe(true);
            });
        });

        describe('takes a string (without class or id identifier) and a class name', () => {
            it('should throw error', () => {
                expect(() => {utils.hasClass('test', 'class')}).toThrow();
            });
        });

        describe('takes an HTMLElement and a wrong class name', () => {
            it('should not find the class and return false', () => {
                expect(utils.hasClass(element, 'wrongClass')).toBe(false);
            });
        })
    });

    describe('getAncestor', () => {
        describe('takes an HTMLElement and a class name', () => {
            it('should return an HTMLElement', () => {
                expect(utils.getAncestor(child, 'class')).toBeInstanceOf(HTMLElement);
                expect(utils.hasClass(utils.getAncestor(child, 'class'), 'class')).toBe(true);
            });
        });

        describe('takes an id and a class name', () => {
            it('should return an HTMLElement', () => {
                expect(utils.getAncestor('#child', 'class')).toBeInstanceOf(HTMLElement);
                expect(utils.hasClass(utils.getAncestor('#child', 'class'), 'class')).toBe(true);
            });
        });

        describe('takes a class name and a class name', () => {
            it('should return an HTMLElement', () => {
                expect(utils.getAncestor('.child', 'class')).toBeInstanceOf(HTMLElement);
                expect(utils.hasClass(utils.getAncestor('.child', 'class'), 'class')).toBe(true);
            });
        });

        describe('takes a string (without class or id identifier) and a class name', () => {
            it('should throw error', () => {
                expect(utils.getAncestor('child', 'class')).toThrow();
            });
        });

        describe('takes an HTMLElement and a wrong class name', () => {
            it('should not find a parent and return input element', () => {
                expect(utils.getAncestor(child, 'wrongclass')).toEqual(child);
            });
        });
    });
});

