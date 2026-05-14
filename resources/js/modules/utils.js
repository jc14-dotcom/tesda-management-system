/**
 * Shared utility functions used across Alpine components.
 * These are pure functions with no side-effects, safe to import anywhere.
 */

export const normalizeTrim = (value) => (value ?? '').trim();

export const collapseSpaces = (value) => normalizeTrim(value).replace(/\s+/g, ' ');

export const sanitizeNameInput = (value) =>
    collapseSpaces(value).replace(/[^A-Za-z\s'-]/g, '');

export const isValidName = (value) =>
    /^[A-Za-z][A-Za-z\s'-]*$/.test(value);

export const isValidEmail = (value) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

export const sanitizeContactNumber = (value) =>
    (value ?? '').replace(/\D/g, '').slice(0, 11);
