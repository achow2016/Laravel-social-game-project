import { mount } from '@vue/test-utils'
import App from '../components/App'
import Welcome from '../components/Welcome'
import Login from '../components/Login'
import Register from '../components/Register'
import Home from '../components/Home'
import ResetPassword from '../components/ResetPassword'
import CharacterBuilder from '../components/CharacterBuilder'
import Chat from '../components/Chat'
import Store from '../components/Store'

describe('my test suites', () => {
	test.only('it works', () => {
		expect(1 + 1).toBe(2)
	})

	test('it works 2', () => {
		expect(1 + 2).toBe(3)
	})

	test('home should mount without crashing', () => {
		const wrapper = mount(Home)
		expect(wrapper).toMatchSnapshot()
	})

	test('store should mount without crashing', () => {
		const wrapper = mount(Store)
		expect(wrapper).toMatchSnapshot()
	})
});